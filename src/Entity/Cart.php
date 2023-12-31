<?php

namespace App\Entity;

use App\Core\Constants;
use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Validator\Constraints\Length;

class Cart
{
    private $purchases = [];

    public function add($product)
    {
        $purchase = new Purchase($product);
        // loop in the cart to see if the product is already exist , if yes then add one quantity.
        foreach ($this->purchases as $testPurchase) {
            if ($testPurchase->getProduct()->getIdProduct() == $product->getIdProduct()) {
                $newQuantity = $testPurchase->getQuantity() + 1;
                $testPurchase->update($newQuantity);
                return true;
            }
        }
        // If it a new purchase check if we have at least one in stock
        // Correction negative quantity
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
            $quantity = $newPurchases["quantity"];
            foreach ($this->purchases as $key => $purchase) {
                if (is_numeric($quantity[$key])) {
                    $newQuantity = $quantity[$key];
                    // test if the quantity is zero, if yes then delete the product from the cart
                    if ($newQuantity == 0) {
                        $this->delete($key);
                        return true;
                    }
                    if($newQuantity < 0){
                        $purchase->update($this->purchases[$key]->getQuantity());
                    }
                    // Check if the stock is more the new quantity 
                    else {
                        $purchase->update($newQuantity);
                    }
                }
                else{
                    return false;
                }
            }
            return true;
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
        return round($subTotal,2);
    }
    public function getTVQPrice(){
        return round(($this -> getSubTotal() * (Constants::TVQ)),2);
    }
    public function getTPSPrice(){
        return round(($this -> getSubTotal() * (Constants::TPS)),2);
    }
    public function getShippinCost(){
        $Total = $this->getSubTotal() + $this->getTVQPrice() + $this->getTPSPrice();
        if($Total != 0) return Constants::SHIPPING_COST;
        return 0;
    }
    public function getTotalPrice(){
        $Total = $this->getSubTotal() + $this->getTVQPrice() + $this->getTPSPrice();
        if($Total == 0) return $Total;

        return round(($Total + $this->getShippinCost()),2);
    }
    public function getTotalPriceStripe(){
        return ($this->getTotalPrice()) *100;
    }
    public function empty(){
        $this->purchases = [];
    }
    public function isEmpty(){
       return empty($this->purchases);
    }
}
