<?php

namespace App\Entity;

use App\Core\Constants;
use App\Repository\OrderRepository;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idOrder')]
    private ?int $idOrder = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name:'orderDate')]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE,name:'deliveryDate',nullable:true)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(name:'rateTPS')]
    private ?float $rateTPS = null;

    #[ORM\Column(name:'rateTVQ')]
    private ?float $rateTVQ = null;

    #[ORM\Column(name:'deliveryFee')]
    private ?float $deliveryFee = null;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

    #[ORM\Column(length: 255,name:'stripeIntent')]
    private ?string $stripeIntent = null;

    #[ORM\ManyToOne(targetEntity:Client::class,inversedBy: 'orders',cascade:["persist"])]
    #[ORM\JoinColumn(name:'idClient',referencedColumnName:'idClient',nullable: false)]
    private ?Client $client = null;

    #[ORM\OneToMany(mappedBy: 'theOrder', targetEntity: Purchase::class, orphanRemoval: true,cascade:["persist"])]
    private Collection $purchases;

    public function __construct(string $paymentIntent,Client $client,Cart $cart)
    {
        $this->orderDate = new \DateTime();
        $this->deliveryDate = null;
        $this->rateTPS = Constants::TPS;
        $this->rateTVQ = Constants::TVQ;
        $this->deliveryFee = Constants::SHIPPING_COST;
        $this->state = "In Preparation";
        $this->stripeIntent = $paymentIntent;
        $this->client = $client;
        $this->purchases = new ArrayCollection();

        foreach ($cart->getPurchases() as $purchase) {
            $this->purchases->add($purchase);
            $purchase->setTheOrder($this);
        }
    }

    public function getIdOrder(): ?int
    {
        return $this->idOrder;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }
    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function getRateTPS(): ?float
    {
        return $this->rateTPS;
    }

    public function getRateTVQ(): ?float
    {
        return $this->rateTVQ;
    }

    public function getDeliveryFee(): ?float
    {
        return $this->deliveryFee;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;
        if($state == "Delivered"){
            $this->deliveryDate = new \DateTime();
        }
        else{
            $this->deliveryDate = null;
        }
        return $this;
    }
    public function getStripeIntent(): ?string
    {
        return $this->stripeIntent;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }
    public function getSubtotal(){
        $total = 0;
        foreach ($this->purchases as $purchase) {
            $total += $purchase->getTotalPrice();
        }
        return $total;
    }
    public function getTotal(){
        return $this->getSubTotal() + $this->getTVQPrice() + $this->getTPSPrice();
    }
    public function getTVQPrice(){
        return round(($this -> getSubTotal() * $this->rateTVQ),2);
    }
    public function getTPSPrice(){
        return round(($this -> getSubTotal() * $this->rateTPS),2);
    }
    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }
    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setTheOrder($this);
        }

        return $this;
    }
    public function isMine($user){
        if($this->client != $user){
            return false;
        }
        return true;
    }
}
