<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
class Product 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idProduct')]
    private ?int $idProduct = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(min:3, minMessage:"The name must have {{ limit }} characters minimum")]
    #[Assert\Length(max:50, maxMessage:"The name must have {{ limit }} characters maximum")]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\Type(type:"float", message:"The price must be a number")]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: "products", cascade: ["persist"])]
    #[ORM\JoinColumn(name: 'idCategory', referencedColumnName: 'idCategory')]
    #[Assert\NotBlank()]
    private $category;

    #[ORM\Column(name: 'stockQuantity')]
    private ?int $stockQuantity = null;

    #[ORM\Column(length: 1024, nullable: true)]
    #[Assert\Length(min:5, minMessage:"The description must have {{ limit }} characters minimum")]
    #[Assert\Length(max:1000, maxMessage:"The description must have {{ limit }} characters maximum")]
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }
    public function setCategory(Category $category):self
    {
        $this->category = $category;
        return $this;
    }
    public function getStockQuantity(): ?int
    {
        return $this->stockQuantity;
    }

    public function setStockQuantity(int $stockQuantity): self
    {
        $this->stockQuantity = $stockQuantity;
        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImgPath(): ?string
    {
        return $this->imgPath;
    }

    public function setImgPath(?string $imgPath): self
    {
        $this->imgPath = $imgPath;

        return $this;
    }
    public function sold($quantity){
        $newQuantity = $this->stockQuantity - $quantity;
        $this->stockQuantity = $newQuantity;
        if($newQuantity<=0) { 
            return false;
        }
        return true;
    }
}
