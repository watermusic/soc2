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
class Player
{

    use Timestampable;

    /**
     * @var integer
     *
     * @Serializer\Expose
     *
     */
    protected $id;

    /**
     * @var string
     *
     * @Serializer\Expose
     */
    protected $name = "";

    /**
     * @var Team
     *
     * @Serializer\Expose
     */
    protected $team;

    /**
     * @var Position
     *
     * @Serializer\Expose
     */
    protected $position;

    /**
     * @var float
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("vkPreis")
     */
    protected $vkPreis = 0.0;

    /**
     * @var float
     */
    protected $ekPreis = 0.0;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var float
     *
     * @Serializer\Expose
     */
    protected $note = 3.5;

    /**
     * @var float
     *
     * @Serializer\Expose
     */
    protected $punkte = 0.0;

    /**
     * @var string
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("thumbUrl")
     */
    protected $thumbUrl = "";

    /**
     * Player constructor.
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
     * Set name
     *
     * @param string $name
     * @return Player
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
     * Set team
     *
     * @param Team $team
     * @return Player
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set position
     *
     * @param Position $position
     * @return Player
     */
    public function setPosition(Position $position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return Position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set vkPreis
     *
     * @param float $vkPreis
     * @return Player
     */
    public function setVkPreis($vkPreis)
    {
        $this->vkPreis = $vkPreis;

        return $this;
    }

    /**
     * Get vkPreis
     *
     * @return float
     */
    public function getVkPreis()
    {
        return $this->vkPreis;
    }

    /**
     * Set ekPreis
     *
     * @param float $ekPreis
     * @return Player
     */
    public function setEkPreis($ekPreis)
    {
        $this->ekPreis = $ekPreis;

        return $this;
    }

    /**
     * Get ekPreis
     *
     * @return float
     */
    public function getEkPreis()
    {
        return $this->ekPreis;
    }

    /**
     * Set User
     *
     * @param User $user
     * @return Player
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get kaufer
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set note
     *
     * @param float $note
     * @return Player
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return float
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set punkte
     *
     * @param float $punkte
     * @return Player
     */
    public function setPunkte($punkte)
    {
        $this->punkte = $punkte;

        return $this;
    }

    /**
     * Get punkte
     *
     * @return float
     */
    public function getPunkte()
    {
        return $this->punkte;
    }

    /**
     * @return string
     */
    public function getThumbUrl()
    {
        return $this->thumbUrl;
    }

    /**
     * @param string $thumbUrl
     * @return Player
     */
    public function setThumbUrl($thumbUrl)
    {
        $this->thumbUrl = $thumbUrl;

        return $this;
    }

}
