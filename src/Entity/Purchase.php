<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[ORM\Table(name: '`Purchases`')]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idPurchase')]
    private ?int $idPurchase = null;

    #[ORM\ManyToOne(targetEntity:Product::class,inversedBy: 'Purchases', cascade: ["persist"])]
    #[ORM\JoinColumn(name:'idProduct',referencedColumnName:'idProduct',nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity:Order::class,inversedBy:'Purchases',cascade:["persist"])]
    #[ORM\JoinColumn(name:'idOrder',referencedColumnName:'idOrder',nullable: false)]
    private ?Order $theOrder = null;
    

    // YANNICK: Correction issue Classe Achat #9
    public function __construct($product)
    {
        $this -> product = $product;
        $this -> quantity = 1;
        $this -> price = $product -> getPrice();
    }
    public function getIdPurchase(): ?int
    {
        return $this->idPurchase;
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
    public function getTheOrder(): ?Order
    {
        return $this->theOrder;
    }
    public function setTheOrder(?Order $theOrder): self
    {
        $this->theOrder = $theOrder;

        return $this;
    }
    public function update($quantity){
        $this -> quantity = $quantity;
    }
    public function getTotalPrice(){
        return $this -> price * $this -> quantity;
    }
    public function setProductQuantity(){
        $this->product->reduceStockQuantity($this->quantity);
    }
}
