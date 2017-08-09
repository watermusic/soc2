<?php

namespace AppBundle\Command;

use AppBundle\Entity\Match;
use AppBundle\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\Range;


class MatchesSyncCommand extends ContainerAwareCommand
{

    const MATCHDAY_MIN = 1;

    const MATCHDAY_MAX = 34;

    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var Collection|Team[]
     */
    protected $teams;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('soc:matches:sync')
            ->setDescription('Syncs the matches form `bundesliga.com` with our storage.')
            ->setHelp(
                <<<EOT
                The <info>%command.name%</info> command syncs the matches from `bundesliga.com` with our storage.
EOT
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->om = $this->doctrine->getManager();
        $this->teams = $this->doctrine->getRepository('AppBundle:Team')->findAll();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clearTables();

        $progress = new ProgressBar($output, self::MATCHDAY_MAX);
        $progress->setMessage('Start');
        $progress->start();

        foreach ( range(self::MATCHDAY_MIN, self::MATCHDAY_MAX) as $matchDay) {

            $matchData = $this->getMatchdayData($matchDay);
            $progress->setMessage('Importing matchday #' . $matchDay);

            foreach ( range(0, 8) as $matchNum) {
                $this->createMatch($matchData[$matchNum]);
            }

            $progress->advance();
        }

        $this->om->flush();
        $progress->finish();

//
    }

    /**
     * @param array $matchData
     */
    protected function createMatch($matchData)
    {
        $match = new Match();
        $match
            ->setMatchDay($matchData['matchday_l'])
            ->setKickoffAt(new \DateTime($matchData['kickoffTime_dt']))
            ->setGuestTeam($this->findTeamByExternalName($matchData['guestTeamName_tlc']))
            ->setHomeTeam($this->findTeamByExternalName($matchData['homeTeamName_tlc']))
            ;
        $this->om->persist($match);
    }

    protected function getMatchdayData($matchday)
    {

        $validator = $this->getContainer()->get('validator');
        $violationList = $validator->validate($matchday, new Range(['min' => self::MATCHDAY_MIN, 'max' => self::MATCHDAY_MAX]));

        if( $violationList->count() !== 0) {
            throw new \RuntimeException(sprintf('The matchday has to be a value between %s and %s. %s is given', self::MATCHDAY_MIN, self::MATCHDAY_MAX, $matchday));
        }

        $url = "http://www.bundesliga.com/fsSolrSearch/search?wt=json&rows=150&sort=kickoffTimeMS_l+asc&q=indexName%3ASyncMatches+AND+competitionID_tlc%3ADFL-COM-000001+AND+seasonID_tlc%3A2017%2F2018+AND+matchday_l%3A" . $matchday;
        $content = file_get_contents($url);

        if( !$content ) {
            throw new \RuntimeException(sprintf('Could not receive matchday content from %s. Please check the internet connection.', $url));
        }

        $content = json_decode($content, true);
        return $content['response']['docs'];
    }

    /**
     * @param string
     * @return Team
     */
    protected function findTeamByExternalName($name)
    {
        $result = array_filter($this->teams, function($team) use($name) {
            /** @var Team $team */
            return stripos($name, $team->getName()) !== false;
        });
        return array_values($result)[0];
    }

    /**
     * @return void
     */
    protected function clearTables()
    {
        $connection = $this->doctrine->getConnection();
        $connection->executeQuery('SET foreign_key_checks = 0');
        $connection->executeQuery('TRUNCATE soc_matches');
        $connection->executeQuery('SET foreign_key_checks = 1');
    }

}
