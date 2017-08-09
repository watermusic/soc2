<?php

namespace AppBundle\Entity;

use JMS\Serializer\Annotation as Serializer;
use AppBundle\Doctrine\Traits\Timestampable;

/**
 * Player
 *
 * @Serializer\ExclusionPolicy("all")
 *
 */
class Match
{

    use Timestampable;

    /**
     * @var int
     *
     * @Serializer\Expose
     *
     */
    protected $id;

    /**
     * @var Team
     *
     * @Serializer\Expose
     */
    protected $homeTeam;

    /**
     * @var Team
     *
     * @Serializer\Expose
     */
    protected $guestTeam;

    /**
     * @var \DateTime
     *
     * @Serializer\Expose
     *
     */
    protected $kickoffAt;

    /**
     * @var int
     *
     * @Serializer\Expose
     */
    protected $matchDay;

    /**
     * Match constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set home team
     *
     * @param Team $team
     * @return $this
     */
    public function setHomeTeam(Team $team)
    {
        $this->homeTeam = $team;

        return $this;
    }

    /**
     * Get home team
     *
     * @return Team
     */
    public function getHomeTeam()
    {
        return $this->homeTeam;
    }

    /**
     * @return Team
     */
    public function getGuestTeam()
    {
        return $this->guestTeam;
    }

    /**
     * @param Team $guestTeam
     * @return Match
     */
    public function setGuestTeam($guestTeam)
    {
        $this->guestTeam = $guestTeam;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getKickoffAt()
    {
        return $this->kickoffAt;
    }

    /**
     * @param \DateTime $kickoffAt
     * @return Match
     */
    public function setKickoffAt($kickoffAt)
    {
        $this->kickoffAt = $kickoffAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getMatchDay()
    {
        return $this->matchDay;
    }

    /**
     * @param int $matchDay
     * @return Match
     */
    public function setMatchDay($matchDay)
    {
        $this->matchDay = $matchDay;

        return $this;
    }

}
