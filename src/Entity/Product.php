<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idProduct')]
    private ?int $idProduct = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: "products", cascade: ["persist"])]
    #[ORM\JoinColumn(name: 'idCategory', referencedColumnName: 'idCategory')]
    private $category;

    #[ORM\Column(name: 'stockQuantity')]
    private ?int $stockQuantity = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true, name: 'imgPath')]
    private ?string $imgPath = null;


    public function getIdProduct(): ?int
    {
        return $this->idProduct;
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    // public function setName(string $name): self
    // {
    //     $this->name = $name;

    //     return $this;
    // }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    // public function setPrice(float $price): self
    // {
    //     $this->price = $price;

    //     return $this;
    // }

    public function getCategory(): ?Category
    {
        return $this->category;
    }
    public function getStockQuantity(): ?int
    {
        return $this->stockQuantity;
    }

    // public function setStockQuantity(int $stockQuantity): self
    // {
    //     $this->stockQuantity = $stockQuantity;
    //     return $this;
    // }
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // public function setDescription(?string $description): self
    // {
    //     $this->description = $description;

    //     return $this;
    // }

    public function getImgPath(): ?string
    {
        return $this->imgPath;
    }

    // public function setImgPath(?string $imgPath): self
    // {
    //     $this->imgPath = $imgPath;

    //     return $this;
    // }
    public function sold($quantity){
        $newQuantity = $this->stockQuantity - $quantity;
        $this->stockQuantity = $newQuantity;
        if($newQuantity<=0) { 
            return false;
        }
        return true;
    }
}
