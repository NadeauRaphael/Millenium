<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;

class Cart
{
    private $purchases = [];

    public function add($product, $quantity, $price)
    {
        $purchase = new Purchase($product,$quantity,$price);
        // Loop in the purchases to see if the product is already in the cart, 
        // if yes then adding one quantity to the purchase quantity
        foreach($this->purchases as $test){
          if($test->getProduct()->getIdProduct() == $product->getIdProduct()){
            $newQuantity = $test->getQuantity() + 1;
            $test->update($newQuantity);
            return;
          }
        }
        $this->purchases[] = $purchase;

    }

    public function update($newPurchases)
    {
        if (count($this->purchases) > 0) {
            $quantity = $newPurchases["quantity"];

            foreach ($this->purchases as $key => $purchase) {
                $newQuantity = $quantity[$key];
                // test if the quantity is zero, if yes then delete the product from the cart
                if($newQuantity == 0){
                    $this->delete($key);
                }
                else{
                    $purchase -> update($newQuantity);
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
}
