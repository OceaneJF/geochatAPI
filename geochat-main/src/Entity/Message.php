<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use App\Services\AddressAPIService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("message_basic")]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Range(min: -180, max: 180)]
    private ?float $longitude = null;

    #[ORM\Column]
    #[Assert\Range(min: -90, max: 90)]
    private ?float $latitude = null;

    #[Groups("message_basic", "message_new")]
    #[ORM\Column(type: "text",)]
    #[Assert\NotBlank()]
    private ?string $text = null;
    #[Groups("message_new")]
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank()]
    private ?string $address = null;

    #[Groups("message_basic")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function fillLngLat(AddressAPIService $api): bool
    {
        if ($this->getAddress() && $lnglat = $api->getLngLat($this->getAddress())) {
            $this
                ->setLongitude($lnglat[0])
                ->setLatitude($lnglat[1]);

            return true;
        }
        return false;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
