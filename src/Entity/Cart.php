<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;

class Cart
{
    private $purchases = [];

    public function add($name, $quantity, $price)
    {
        $purchase = new Purchase($name,$quantity,$price);
        $this->purchases[] = $purchase;
    }
    public function update($newPurchases)
    {
        if (count($this->purchases) > 0) {
            foreach ($this->purchases as $key => $purchase) {
            }
        }
    }
    public function getPurchases()
    {
        return $this->purchases;
    }
    public function delete($index)
    {
        if (array_key_exists($index, $this->purchases)) {
            unset($this->purchases[$index]);
        }
    }
}
