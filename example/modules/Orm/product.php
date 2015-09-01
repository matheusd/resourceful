<?php

namespace ExampleApp\Orm;

/**
 * @Entity @Table(name="products")
 **/
class Product
{
    /**
     * @Id @Column(type="integer") @GeneratedValue 
     * @var int
     */
    protected $id;
    
    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;
    
    /**
     * @Column(type="json_array", nullable=true)
     * @var string
     */
     protected $extra;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getExtra()
    {
        return $this->extra;
    }

    public function setExtra($extra)
    {
        $this->extra = $extra;
    }
}