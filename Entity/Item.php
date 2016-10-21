<?php

namespace RB\BoltBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="item")
 * @ORM\Entity(repositoryClass="RB\BoltBundle\Repository\ItemRepository")
 */
class Item
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", length=50, nullable=true)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Column(name="view", type="string", length=255, nullable=true)
     */
    private $view;

    /**
     * @var bool
     *
     * @ORM\Column(name="multiple", type="boolean", nullable=true, options={"default" = false})
     */
    private $multiple;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="meta", type="array", nullable=true)
     */
    private $meta;


    /**
     *
     * @ORM\ManyToOne(targetEntity="RB\BoltBundle\Entity\Type", cascade={"persist"})
     */
    private $type;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="projects", type="object", nullable=true)
     */
    private $projects;




    /**
     * @var boolean
     *
     * @ORM\Column(name="lockme", type="boolean", nullable=true , options={"default" = null})
     */
    private $lockme;




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
     *
     * @return Item
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
     * @return Item
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
     * Set context
     *
     * @param string $context
     *
     * @return Item
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set view
     *
     * @param string $view
     *
     * @return Item
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get view
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set multiple
     *
     * @param boolean $multiple
     *
     * @return Item
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Get multiple
     *
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Set meta
     *
     * @param array $meta
     *
     * @return Item
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get meta
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set projects
     *
     * @param \stdClass $projects
     *
     * @return Item
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;

        return $this;
    }

    /**
     * Get projects
     *
     * @return \stdClass
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Set lockme
     *
     * @param boolean $lockme
     *
     * @return Item
     */
    public function setLockme($lockme)
    {
        $this->lockme = $lockme;

        return $this;
    }

    /**
     * Get lockme
     *
     * @return boolean
     */
    public function getLockme()
    {
        return $this->lockme;
    }

    /**
     * Set type
     *
     * @param \RB\BoltBundle\Entity\Type $type
     *
     * @return Item
     */
    public function setType(\RB\BoltBundle\Entity\Type $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \RB\BoltBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }
}
