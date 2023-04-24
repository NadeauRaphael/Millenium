<?php

namespace App\Entity;

use App\Repository\OrderRepository;
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

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(name:'idClient',referencedColumnName:'idClient',nullable: false)]
    private ?Client $Client = null;

    #[ORM\OneToMany(mappedBy: 'theOrder', targetEntity: Purchase::class, orphanRemoval: true)]
    private Collection $Purchases;

    public function __construct()
    {
        $this->Purchases = new ArrayCollection();
    }

    public function getIdOrder(): ?int
    {
        return $this->idOrder;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getRateTPS(): ?float
    {
        return $this->rateTPS;
    }

    public function setRateTPS(float $rateTPS): self
    {
        $this->rateTPS = $rateTPS;

        return $this;
    }

    public function getRateTVQ(): ?float
    {
        return $this->rateTVQ;
    }

    public function setRateTVQ(float $rateTVQ): self
    {
        $this->rateTVQ = $rateTVQ;

        return $this;
    }

    public function getDeliveryFee(): ?float
    {
        return $this->deliveryFee;
    }

    public function setDeliveryFee(float $deliveryFee): self
    {
        $this->deliveryFee = $deliveryFee;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStripeIntent(): ?string
    {
        return $this->stripeIntent;
    }

    public function setStripeIntent(string $stripeIntent): self
    {
        $this->stripeIntent = $stripeIntent;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->Client;
    }

    public function setClient(?Client $Client): self
    {
        $this->Client = $Client;

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->Purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->Purchases->contains($purchase)) {
            $this->Purchases->add($purchase);
            $purchase->setTheOrder($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->Purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getTheOrder() === $this) {
                $purchase->setTheOrder(null);
            }
        }

        return $this;
    }
}
