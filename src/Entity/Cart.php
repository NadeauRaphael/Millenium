<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Validator\Constraints\Length;

class Cart
{
    private $purchases = [];

    public function add($product, $quantity, $price): bool
    {
        $purchase = new Purchase($product, $quantity, $price);
        foreach ($this->purchases as $testPurchase) {
            if ($testPurchase->getProduct()->getIdProduct() == $product->getIdProduct()) {
                $newQuantity = $testPurchase->getQuantity() + 1;
                // Check the quantity available in stock for the product
                if ($testPurchase->getProduct()->getStockQuantity() >= $newQuantity) {
                    $testPurchase->update($newQuantity);
                    return true;
                }
                else{return false;}
            }
        }
        // If it a new purchase check if we add at least one in stock
        if($purchase->getProduct()->getStockQuantity() > 0){
            $this->purchases[] = $purchase;
            return true;
        }else{
             return false;
        }
    }

    public function update($newPurchases):bool
    {
        $stock = false;
        if (count($this->purchases) > 0) {
            $quantity = $newPurchases["quantity"]; 
            foreach ($this->purchases as $key => $purchase) {
                $newQuantity = $quantity[$key];
                var_dump($key);
                // test if the quantity is zero, if yes then delete the product from the cart
                if ($newQuantity == 0) {
                    $this->delete($key);
                    return true;
                }
                // Check if the stock is more the new quantity 
                else {
                    if($purchase -> getProduct() -> getStockQuantity() >= $newQuantity){
                        $purchase->update($newQuantity);
                        $stock = true;
                    }
                    else{
                        $stock = false;
                    }
                }
            }
            return $stock;
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
