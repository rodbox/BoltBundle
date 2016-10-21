<?php

namespace RB\BoltBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Type
 *
 * @ORM\Table(name="type")
 * @ORM\Entity(repositoryClass="RB\BoltBundle\Repository\TypeRepository")
 */
class Type
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
     * @ORM\Column(name="color", type="string", length=7, options={"default" = "#000"})
     */
    private $color;


    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=30, options={"default" = "fa fa-circle"})
     */
    private $icon;


    /**
     * @var array
     * @ORM\Column(name="hook", type="array")
     */
    private $hook;


    /**
     * @var array
     * @ORM\Column(name="allowInputs", type="array")
     */
    private $allowInputs;

    /**
     * @var array
     * @ORM\Column(name="allowOutputs", type="array")
     */
    private $allowOutputs;

    
    /**
     * @var object
     * @ORM\Column(name="metaform", type="object")
     */
    private $typeForm;



    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;



    /**
     * @var boolean
     *
     * @ORM\Column(name="start", type="boolean", nullable=true, options={"default" = false})
     */
    private $start;


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
     * @return Type
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
     * Set color
     *
     * @param string $color
     *
     * @return Type
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return Type
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set hook
     *
     * @param array $hook
     *
     * @return Type
     */
    public function setHook($hook)
    {
        $this->hook = $hook;

        return $this;
    }

    /**
     * Get hook
     *
     * @return array
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * Set allowInputs
     *
     * @param array $allowInputs
     *
     * @return Type
     */
    public function setAllowInputs($allowInputs)
    {
        $this->allowInputs = $allowInputs;

        return $this;
    }

    /**
     * Get allowInputs
     *
     * @return array
     */
    public function getAllowInputs()
    {
        return $this->allowInputs;
    }

    /**
     * Set allowOutputs
     *
     * @param array $allowOutputs
     *
     * @return Type
     */
    public function setAllowOutputs($allowOutputs)
    {
        $this->allowOutputs = $allowOutputs;

        return $this;
    }

    /**
     * Get allowOutputs
     *
     * @return array
     */
    public function getAllowOutputs()
    {
        return $this->allowOutputs;
    }

    /**
     * Set typeForm
     *
     * @param \stdClass $typeForm
     *
     * @return Type
     */
    public function setTypeForm($typeForm)
    {
        $this->typeForm = $typeForm;

        return $this;
    }

    /**
     * Get typeForm
     *
     * @return \stdClass
     */
    public function getTypeForm()
    {
        return $this->typeForm;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Type
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
     * Set start
     *
     * @param boolean $start
     *
     * @return Type
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return boolean
     */
    public function getStart()
    {
        return $this->start;
    }
}
