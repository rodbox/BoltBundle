<?php

namespace RB\BoltBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Projects
 *
 * @ORM\Table(name="projects")
 * @ORM\Entity(repositoryClass="RB\BoltBundle\Repository\ProjectsRepository")
 */
class Projects
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(name="security", type="array", nullable=true)
     */
    private $security;

    /**
     * @var array
     *
     * @ORM\Column(name="pointers", type="array", nullable=true)
     */
    private $pointers;

    /**
     * @var object
     *
     * @ORM\Column(name="bolt", type="object", nullable=true)
     */
    private $bolt;

    /**
     * @var string
     *
     * @ORM\Column(name="do", type="string", length=255, nullable=true)
     */
    private $do;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Projects
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
     * Set description
     *
     * @param string $description
     *
     * @return Projects
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set security
     *
     * @param array $security
     *
     * @return Projects
     */
    public function setSecurity($security)
    {
        $this->security = $security;

        return $this;
    }

    /**
     * Get security
     *
     * @return array
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * Set pointers
     *
     * @param array $pointers
     *
     * @return Projects
     */
    public function setPointers($pointers)
    {
        $this->pointers = $pointers;

        return $this;
    }

    /**
     * Get pointers
     *
     * @return array
     */
    public function getPointers()
    {
        return $this->pointers;
    }

    /**
     * Set do
     *
     * @param string $do
     *
     * @return Projects
     */
    public function setDo($do)
    {
        $this->do = $do;

        return $this;
    }

    /**
     * Get do
     *
     * @return string
     */
    public function getDo()
    {
        return $this->do;
    }

    /**
     * Set bolt
     *
     * @param \stdClass $bolt
     *
     * @return Projects
     */
    public function setBolt($bolt)
    {
        $this->bolt = $bolt;

        return $this;
    }

    /**
     * Get bolt
     *
     * @return \stdClass
     */
    public function getBolt()
    {
        return $this->bolt;
    }
}
