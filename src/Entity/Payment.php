<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $paymentAmount = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentCode = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invoice $invoice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentAmount(): ?float
    {
        return $this->paymentAmount;
    }

    public function setPaymentAmount(float $paymentAmount): self
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    public function getPaymentCode(): ?string
    {
        return $this->paymentCode;
    }

    public function setPaymentCode(string $paymentCode): self
    {
        $this->paymentCode = $paymentCode;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }
}
