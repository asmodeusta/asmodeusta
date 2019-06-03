<?php

class Product
{

    private $type;
    private $title;
    private $description;
    private $price;
    private $quantity;

    public function __construct()
    {
        // initialization
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price)
    {
        $this->price = $price;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

}

// Using

$simpleProductPrototype = new Product();
$simpleProductPrototype->setType('simple');

$virtualProductPrototype =new Product();
$virtualProductPrototype->setType('virtual');

$simpleProduct = clone $simpleProductPrototype;
$virtualProduct = clone $virtualProductPrototype;