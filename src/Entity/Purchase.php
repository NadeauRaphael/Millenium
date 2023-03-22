<?php

namespace App\Entity;

class Purchase
{
    private $product;
    private $quantity;
    private $price;

    public function __construct($product, $quantity, $price)
    {
        $this -> product = $product;
        $this -> quantity = $quantity;
        $this -> price = $price;
    }

    public function update($quantity){
        $this -> quantity = $quantity;
    }
    public function getProduct(){
        return $this -> product;
    }
    public function getQuantity(){
        return $this -> quantity;
    }
    public function getPrice(){
        return $this -> price;
    }
}
