<?php

namespace App\Entity;

use App\Repository\CostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CostRepository::class)]
class Cost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $documentNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transactionNumber = null;

    #[ORM\Column]
    private ?float $cost = null;

    #[ORM\ManyToOne(inversedBy: 'costs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $item = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $purchaseDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $purchasedFrom = null;

    #[ORM\ManyToMany(targetEntity: Category::class)]
    private Collection $categories;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(string $documentNumber): self
    {
        $this->documentNumber = $documentNumber;

        return $this;
    }

    public function getTransactionNumber(): ?string
    {
        return $this->transactionNumber;
    }

    public function setTransactionNumber(?string $transactionNumber): self
    {
        $this->transactionNumber = $transactionNumber;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getItem(): ?string
    {
        return $this->item;
    }

    public function setItem(string $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(\DateTimeInterface $purchaseDate): self
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    public function getPurchasedFrom(): ?string
    {
        return $this->purchasedFrom;
    }

    public function setPurchasedFrom(?string $purchasedFrom): self
    {
        $this->purchasedFrom = $purchasedFrom;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /** @param Collection<int, Category> $categories*/
    public function setCategories(Collection $categories): self
    {
        $this ->categories = $categories;
        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
