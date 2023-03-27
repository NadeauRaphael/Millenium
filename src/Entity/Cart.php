<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Validator\Constraints\Length;

class Cart
{
    private $purchases = [];

    public function add($product, $quantity, $price)
    {
        $purchase = new Purchase($product, $quantity, $price);
        // loop in the cart to see if the product is already exist , if yes then add one quantity.
        foreach ($this->purchases as $testPurchase) {
            if ($testPurchase->getProduct()->getIdProduct() == $product->getIdProduct()) {
                $newQuantity = $testPurchase->getQuantity() + 1;
                $testPurchase->update($newQuantity);
            }
        }
        // If it a new purchase check if we have at least one in stock
        if ($purchase->getProduct()->getStockQuantity() > 0) {
            $this->purchases[] = $purchase;
            return true;
        } else {
            return false;
        }
    }

    public function update($newPurchases)
    {
        if (count($this->purchases) > 0) {
            var_dump($newPurchases);
            die();
            $quantity = $newPurchases["quantity"];
            foreach ($this->purchases as $key => $purchase) {
                $newQuantity = $quantity[$key];
                // test if the quantity is zero, if yes then delete the product from the cart
                if ($newQuantity == 0) {
                    $this->delete($key);
                    return true;
                }
                // Check if the stock is more the new quantity 
                else {
                    $purchase->update($newQuantity);
                }
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
    public function getSubTotal()
    {
        $subTotal = 0;
        foreach ($this->purchases as $purchase) {
            $subTotal += $purchase->getPrice() * $purchase->getQuantity();
        }
        return $subTotal;
    }
}
