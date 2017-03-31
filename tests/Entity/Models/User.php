<?php

namespace Polymer\Tests\Entity\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="fk_user_company1_idx", columns={"company_id"})})
 * @ORM\Entity(repositoryClass="Polymer\Tests\Entity\Repositories\UserRepository")
 */
class User
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
     * @ORM\Column(name="username", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="pwd", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $pwd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sex", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $sex;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=11, precision=0, scale=0, nullable=true, unique=false)
     */
    private $phone;

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
     * @var \Polymer\Tests\Entity\Models\Company
     *
     * @ORM\ManyToOne(targetEntity="Polymer\Tests\Entity\Models\Company")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $company;


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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set pwd
     *
     * @param string $pwd
     *
     * @return User
     */
    public function setPwd($pwd)
    {
        $this->pwd = $pwd;

        return $this;
    }

    /**
     * Get pwd
     *
     * @return string
     */
    public function getPwd()
    {
        return $this->pwd;
    }

    /**
     * Set sex
     *
     * @param boolean $sex
     *
     * @return User
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return boolean
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set created
     *
     * @param integer $created
     *
     * @return User
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
     * @return User
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
     * Set company
     *
     * @param \Entity\Models\Company $company
     *
     * @return User
     */
    public function setCompany(\Entity\Models\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Entity\Models\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}

