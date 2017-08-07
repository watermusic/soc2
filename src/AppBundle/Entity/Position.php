<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use JMS\Serializer\Annotation as Serializer;

/**
 * Position
 *
 * @ORM\Table(name="soc_position",indexes={@ORM\Index(columns={"name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PositionRepository")
 */
class Position
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Player", mappedBy="position", cascade={"all"}, orphanRemoval=true)
     *
     * @Serializer\Exclude
     */
    protected $players;

    /**
     * @var string
     *
     * @ORM\Column(name="shortcut", type="string", length=255)
     */
    protected $shortcut;

    /**
     * @var string
     *
     * @ORM\Column(name="color_name", type="string", length=255)
     */
    protected $colorName;


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
     * @return Position
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
     * @return Position
     */
    public function addPlayer(Player $player)
    {
        if (!$this->hasPlayer($player)) {
            $this->players[] = $player;
            $player->setPosition($this);
        }

        return $this;
    }

    /**
     * Remove player
     *
     * @param Player $player
     * @return Position
     */
    public function removePlayer(Player $player)
    {
        $this->players->removeElement($player);
        $player->setPosition(null);
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
     * @return Position
     */
    public function setPlayers($players)
    {
        $this->players = $players;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortcut()
    {
        return $this->shortcut;
    }

    /**
     * @param string $shortcut
     * @return Position
     */
    public function setShortcut($shortcut)
    {
        $this->shortcut = $shortcut;
        return $this;
    }

    /**
     * @return string
     */
    public function getColorName()
    {
        return $this->colorName;
    }

    /**
     * @param string $colorName
     * @return Position
     */
    public function setColorName($colorName)
    {
        $this->colorName = $colorName;
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
