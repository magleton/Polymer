<?php

namespace Polymer\Tests\Entity\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 *
 * @ORM\Table(name="profile", indexes={@ORM\Index(name="fk_profile_user_idx", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Polymer\Tests\Entity\Repositories\ProfileRepository")
 */
class Profile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="real_name", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $realName;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=256, precision=0, scale=0, nullable=true, unique=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="live_phone", type="string", length=11, precision=0, scale=0, nullable=true, unique=false)
     */
    private $livePhone;

    /**
     * @var integer
     *
     * @ORM\Column(name="created", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $created;

    /**
     * @var integer
     *
     * @ORM\Column(name="updated", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $updated;

    /**
     * @var \Entity\Models\User
     *
     * @ORM\ManyToOne(targetEntity="Entity\Models\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $user;


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
     * Set realName
     *
     * @param string $realName
     *
     * @return Profile
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;

        return $this;
    }

    /**
     * Get realName
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Profile
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set livePhone
     *
     * @param string $livePhone
     *
     * @return Profile
     */
    public function setLivePhone($livePhone)
    {
        $this->livePhone = $livePhone;

        return $this;
    }

    /**
     * Get livePhone
     *
     * @return string
     */
    public function getLivePhone()
    {
        return $this->livePhone;
    }

    /**
     * Set created
     *
     * @param integer $created
     *
     * @return Profile
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return integer
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param integer $updated
     *
     * @return Profile
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return integer
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set user
     *
     * @param \Entity\Models\User $user
     *
     * @return Profile
     */
    public function setUser(\Entity\Models\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Entity\Models\User
     */
    public function getUser()
    {
        return $this->user;
    }
}

