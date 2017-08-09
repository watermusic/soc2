<?php

namespace AppBundle\Entity;

use JMS\Serializer\Annotation as Serializer;
use AppBundle\Doctrine\Traits\Timestampable;

/**
 * Score
 *
 * @Serializer\ExclusionPolicy("all")
 *
 */
class Lineup implements \AppBundle\Contracts\Timestampable
{

    use Timestampable;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var User
     **/
    protected $user;

    /**
     * @var integer
     *
     * @Serializer\Expose
     */
    protected $matchday;

    /**
     * @var array
     *
     * @Serializer\Expose
     */
    protected $data;

    /**
     * Lineup constructor.
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
     * Set user
     *
     * @param User $user
     * @return Lineup
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set matchday
     *
     * @param integer $matchday
     * @return Lineup
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
     * Set data
     *
     * @param array $data
     * @return Lineup
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

}
