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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Collection|Player[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Player", mappedBy="team", cascade={"all"}, orphanRemoval=true)
     *
     * @Serializer\Type("array<SOC\Bundle\SocBundle\Entity\Player>")
     * @Serializer\Exclude
     */
    private $players;



    public function __construct()
    {
        $this->players = new ArrayCollection();
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
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

}
