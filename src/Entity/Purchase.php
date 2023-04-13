<?php

namespace App\Entity;

class Purchase
{
    private $product;
    private $quantity;
    private $price;

    // YANNICK: Correction issue Classe Achat #9
    public function __construct($product)
    {
        $this -> product = $product;
        $this -> quantity = 1;
        $this -> price = $product -> getPrice();
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
