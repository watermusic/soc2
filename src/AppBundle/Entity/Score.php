<?php

namespace AppBundle\Entity;

/**
 * Score
 *
 */
class Score
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var User
     **/
    protected $player;

    /**
     * @var integer
     */
    protected $matchday;

    /**
     * @var integer
     */
    protected $score;

    /**
     * Score constructor.
     */
    public function __construct()
    {
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
     * Set player
     *
     * @param User $player
     * @return Score
     */
    public function setPlayer(User $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return User
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set matchday
     *
     * @param integer $matchday
     * @return Score
     */
    public function setMatchday($matchday)
    {
        $this->matchday = $matchday;

        return $this;
    }

    /**
     * Get matchday
     *
     * @return integer 
     */
    public function getMatchday()
    {
        return $this->matchday;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return Score
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

}
