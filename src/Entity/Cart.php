<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;

class Cart
{
    private $purchases = [];

    public function add($product, $quantity, $price)
    {
        $purchase = new Purchase($product,$quantity,$price);

        $this->purchases[] = $purchase;
    }

    public function update($newPurchases)
    {
        if (count($this->purchases) > 0) {
            $quantity = $newPurchases["quantity"];

            foreach ($this->purchases as $key => $purchase) {
                $newQuantity = $quantity[$key];
                $purchase -> update($newQuantity);
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
