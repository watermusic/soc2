<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{

    /**
     * @var Collection|Score[]
     */
    protected $scores;

    /**
     * @var Collection|Lineup[]
     */
    protected $lineups;

    /**
     * @var Collection|Player[]
     */
    protected $players;

    /**
     * @var string
     */
    protected $avatar = '';

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->scores = new ArrayCollection();
        $this->players = new ArrayCollection();
        $this->lineups = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @return Collection|Score[]
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * @param Score $score
     * @return $this
     */
    public function addScore(Score $score)
    {
        if (!$this->scores->contains($score)) {
            $score->setPlayer($this);
            $this->scores->add($score);
        }
        return $this;
    }

    /**
     * @param Score $score
     * @return $this
     */
    public function removeScore(Score $score)
    {
        if ($this->scores->contains($score)) {
            $score->setPlayer(null);
            $this->scores->removeElement($score);
        }
        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayer()
    {
        return $this->players;
    }

    /**
     * @param Player $player
     * @return $this
     */
    public function addPlayer(Player $player)
    {
        if (!$this->players->contains($player)) {
            $player->setUser($this);
            $this->players->add($player);
        }
        return $this;
    }

    /**
     * @param Player $player
     * @return $this
     */
    public function removePlayer(Player $player)
    {
        if ($this->players->contains($player)) {
            $player->setUser(null);
            $this->players->removeElement($player);
        }
        return $this;
    }

    /**
     * @return Collection|Lineup[]
     */
    public function getLineups()
    {
        return $this->lineups;
    }

    /**
     * @param $index
     * @return Lineup
     */
    public function getLineup($index)
    {
        return $this->lineups->get($index);
    }

    /**
     * @param Lineup $lineup
     * @return $this
     */
    public function addLineup(Lineup $lineup)
    {
        if (!$this->lineups->contains($lineup)) {
            $lineup->setUser($this);
            $this->lineups->add($lineup);
        }
        return $this;
    }

    /**
     * @param Lineup $lineup
     * @return $this
     */
    public function removeLineup(Lineup $lineup)
    {
        if ($this->players->contains($lineup)) {
            $lineup->setUser(null);
            $this->players->removeElement($lineup);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

}