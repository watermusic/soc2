<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Player
 *
 * @ORM\Table(name="soc_matches",indexes={@ORM\Index(columns={"match_day"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MatchesRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 */
class Match
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose
     *
     */
    protected $id;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="players", cascade={"all"}, fetch="EAGER")
     *
     * @Serializer\Expose
     */
    protected $homeTeam;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="players", cascade={"all"}, fetch="EAGER")
     *
     * @Serializer\Expose
     */
    protected $guestTeam;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\Expose
     *
     */
    protected $kickoffAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     *
     * @Serializer\Expose
     *
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     *
     * @Serializer\Expose
     *
     */
    protected $updatedAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
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

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }



}
