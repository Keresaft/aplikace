<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Details $details = null;

    #[ORM\OneToMany(mappedBy: 'customerID', targetEntity: Invoice::class, orphanRemoval: true)]
    private Collection $invoiceID;

    public function __construct()
    {
        $this->invoiceID = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDetails(): ?Details
    {
        return $this->details;
    }

    public function setDetails(?Details $details): self
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoiceID(): Collection
    {
        return $this->invoiceID;
    }

    public function addInvoiceID(Invoice $invoiceID): self
    {
        if (!$this->invoiceID->contains($invoiceID)) {
            $this->invoiceID->add($invoiceID);
            $invoiceID->setCustomerID($this);
        }

        return $this;
    }

    public function removeInvoiceID(Invoice $invoiceID): self
    {
        if ($this->invoiceID->removeElement($invoiceID)) {
            // set the owning side to null (unless already changed)
            if ($invoiceID->getCustomerID() === $this) {
                $invoiceID->setCustomerID(null);
            }
        }

        return $this;
    }
}
