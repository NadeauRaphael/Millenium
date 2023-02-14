<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"idCategory")]
    private ?int $idCategory = null;

    #[ORM\Column(length: 100)]
    private ?string $category = null;

    #[ORM\OneToMany(targetEntity:Product::class, mappedBy:"category", fetch:"LAZY")]
    private $product;


    public function getIdCategory(): ?int
    {
        return $this->idCategory;
    }
    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getProduct(): Collection {
        return $this->product;
    }
}
