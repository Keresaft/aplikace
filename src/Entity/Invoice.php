<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $service = null;

    #[ORM\ManyToOne(inversedBy: 'invoiceID')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customerID = null;

    #[ORM\Column]
    private ?bool $paymentStatus = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: Payment::class, orphanRemoval: true)]
    private Collection $payments;

    #[ORM\Column]
    private array $customerJson = [];

    #[ORM\Column]
    private array $userJson = [];

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

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

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getCustomerID(): ?Customer
    {
        return $this->customerID;
    }

    public function setCustomerID(?Customer $customerID): self
    {
        $this->customerID = $customerID;

        return $this;
    }

    public function isPaymentStatus(): ?bool
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(bool $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setInvoice($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getInvoice() === $this) {
                $payment->setInvoice(null);
            }
        }

        return $this;
    }

    public function getCustomerJson(): array
    {
        return $this->customerJson;
    }

    public function setCustomerJson(array $customerJson): self
    {
        $this->customerJson = $customerJson;

        return $this;
    }

    public function getUserJson(): array
    {
        return $this->userJson;
    }

    public function setUserJson(array $userJson): self
    {
        $this->userJson = $userJson;

        return $this;
    }

}
