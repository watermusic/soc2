<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Player
 *
 * @ORM\Table(name="soc_player",indexes={@ORM\Index(columns={"name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PlayerRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 */
class Player
{
    /**
     * @var integer
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Serializer\Expose
     */
    protected $name = "";

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="players", cascade={"all"}, fetch="EAGER")
     *
     * @Serializer\Expose
     */
    protected $team;

    /**
     * @var Position
     *
     * @ORM\ManyToOne(targetEntity="Position", inversedBy="players", cascade={"all"}, fetch="EAGER")
     *
     * @Serializer\Expose
     */
    protected $position;

    /**
     * @var float
     *
     * @ORM\Column(name="vk_preis", type="decimal", scale=2)
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("vkPreis")
     */
    protected $vkPreis = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(name="ek_preis", type="decimal", scale=2)
     */
    protected $ekPreis = 0.0;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="players", cascade={"all"}, fetch="EAGER")
     */
    protected $user;

    /**
     * @var float
     *
     * @ORM\Column(name="note", type="float")
     *
     * @Serializer\Expose
     */
    protected $note = 3.5;

    /**
     * @var float
     *
     * @ORM\Column(name="punkte", type="float")
     *
     * @Serializer\Expose
     */
    protected $punkte = 0.0;

    /**
     * @var string
     *
     * @ORM\Column(name="thumb_url", type="string", length=255)
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("thumbUrl")
     */
    protected $thumbUrl = "";

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * Player constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
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

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @return Player
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     * @return Player
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }



}
