<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use JMS\Serializer\Annotation as Serializer;

/**
 * Team
 *
 * @ORM\Table(name="soc_team",indexes={@ORM\Index(columns={"name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TeamRepository")
 */
class Team
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var Collection|Player[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Player", mappedBy="team", cascade={"all"}, orphanRemoval=true)
     *
     * @Serializer\Type("array<AppBundle\Entity\Player>")
     * @Serializer\Exclude
     */
    protected $players;

    /**
     * @var Collection|Match[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Match", mappedBy="homeTeam", cascade={"all"}, orphanRemoval=true)
     *
     * @Serializer\Type("array<AppBundle\Entity\Match>")
     * @Serializer\Exclude
     */
    protected $homeMatches;

    /**
     * @var Collection|Match[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Match", mappedBy="guestTeam", cascade={"all"}, orphanRemoval=true)
     *
     * @Serializer\Type("array<AppBundle\Entity\Match>")
     * @Serializer\Exclude
     */
    protected $guestMatches;

    /**
     * Team constructor.
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->homeMatches = new ArrayCollection();
        $this->guestMatches = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param Player $player
     * @return bool
     */
    public function hasPlayer(Player $player)
    {
        return $this->players->contains($player);
    }

    /**
     * Add player
     *
     * @param Player $player
     * @return Team
     */
    public function addPlayer(Player $player)
    {
        if (!$this->hasPlayer($player)) {
            $this->players[] = $player;
            $player->setTeam($this);
        }

        return $this;
    }

    /**
     * Remove player
     *
     * @param Player $player
     * @return Team
     */
    public function removePlayer(Player $player)
    {
        $this->players->removeElement($player);
        $player->setTeam(null);
        return $this;
    }

    /**
     * Get player
     *
     * @return Collection|Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }


    /**
     * @param Collection|Player[] $players
     */
    public function setPlayers($players)
    {
        $this->players = $players;
    }

    /**
     * @return Match[]|Collection
     */
    public function getHomeMatches()
    {
        return $this->homeMatches;
    }

    /**
     * @param Match[]|Collection $homeMatches
     * @return Team
     */
    public function setHomeMatches($homeMatches)
    {
        $this->homeMatches = $homeMatches;

        return $this;
    }

    /**
     * @return Match[]|Collection
     */
    public function getGuestMatches()
    {
        return $this->guestMatches;
    }

    /**
     * @param Match[]|Collection $guestMatches
     * @return Team
     */
    public function setGuestMatches($guestMatches)
    {
        $this->guestMatches = $guestMatches;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

}
