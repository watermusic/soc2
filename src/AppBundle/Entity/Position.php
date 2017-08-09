<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use JMS\Serializer\Annotation as Serializer;

/**
 * Position
 *
 */
class Position
{

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Collection|Player[]

     * @Serializer\Exclude
     */
    protected $players;

    /**
     * @var string
     */
    protected $shortcut;

    /**
     * @var string
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
     * Position constructor.
     */
    public function __construct()
    {
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
