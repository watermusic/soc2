<?php

namespace AppBundle\Entity;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

/**
 * MatchesRepository
 *
 */
class MatchesRepository extends EntityRepository
{

    /**
     * @param \DateTime $date
     *
     * @return int
     */
    public function findNextMatchday(\DateTime $date = null)
    {
        if($date === null) {
            $date = new \DateTime();
        }

        $qb = $this->createQueryBuilder('m');
        /** @var Match $match */
        $match = $qb
            ->andWhere($qb->expr()->gte('m.kickoffAt', ':date'))
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if($match === null) {
            return 34;
        }    

        return $match->getMatchDay();
    }

    /**
     * @param \DateTime $date
     *
     * @return array
     */
    public function findNextMatchdayMatches(\DateTime $date = null)
    {
        $matchDay = $this->findNextMatchday($date);

        $qb = $this->createQueryBuilder('m');
        /** @var Match $match */
        return $qb
            ->andWhere($qb->expr()->eq('m.matchDay', ':matchDay'))
            ->setParameter('matchDay', $matchDay)
            ->getQuery()
            ->getResult()
        ;
    }

}
