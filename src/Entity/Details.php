<?php

namespace App\Entity;

use App\Repository\DetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsRepository::class)]
class Details
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $ico = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $dico = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 50)]
    private ?string $zipCode = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phoneNumber = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIco(): ?string
    {
        return $this->ico;
    }

    public function setIco(string $ico): self
    {
        $this->ico = $ico;

        return $this;
    }

    public function getDico(): ?string
    {
        return $this->dico;
    }

    public function setDico(string $dico): self
    {
        $this->dico = $dico;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'ico' => $this->ico,
            'dico' => $this->dico,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'zipCode' => $this->zipCode,
            'phoneNumber' => $this->phoneNumber,
        ];

        return $data;
    }


}
