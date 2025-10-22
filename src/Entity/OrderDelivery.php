<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'orders_delivery')]
class OrderDelivery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['order_delivery'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['order_delivery'])]
    private int $orderId;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?int $countryId = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?int $regionId = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?int $cityId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?string $amount = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Groups(['order_delivery'])]
    private bool $typeId = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Groups(['order_delivery'])]
    private bool $calculateTypeId = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?\DateTimeInterface $timeMin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?\DateTimeInterface $timeMax = null;

    #[ORM\Column(type: Types::STRING, length: 500)]
    #[Groups(['order_delivery'])]
    private string $fullAddress;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?string $address = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?string $building = null;

    #[ORM\Column(type: Types::STRING, length: 30, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?string $apartmentOffice = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?string $postalCode = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?string $trackingNumber = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?int $carrierId = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?bool $offsetReason = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?\DateTimeInterface $offsetDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?\DateTimeInterface $proposedDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['order_delivery'])]
    private ?\DateTimeInterface $shipDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['order_delivery'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['order_delivery'])]
    private \DateTimeInterface $updatedAt;

    #[ORM\OneToOne(targetEntity: Order::class, inversedBy: 'delivery')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    private Order $order;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    public function setCountryId(?int $countryId): self
    {
        $this->countryId = $countryId;
        return $this;
    }

    public function getRegionId(): ?int
    {
        return $this->regionId;
    }

    public function setRegionId(?int $regionId): self
    {
        $this->regionId = $regionId;
        return $this;
    }

    public function getCityId(): ?int
    {
        return $this->cityId;
    }

    public function setCityId(?int $cityId): self
    {
        $this->cityId = $cityId;
        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getTypeId(): bool
    {
        return $this->typeId;
    }

    public function setTypeId(bool $typeId): self
    {
        $this->typeId = $typeId;
        return $this;
    }

    public function getCalculateTypeId(): bool
    {
        return $this->calculateTypeId;
    }

    public function setCalculateTypeId(bool $calculateTypeId): self
    {
        $this->calculateTypeId = $calculateTypeId;
        return $this;
    }

    public function getTimeMin(): ?DateTimeInterface
    {
        return $this->timeMin;
    }

    public function setTimeMin(?DateTimeInterface $timeMin): self
    {
        $this->timeMin = $timeMin;
        return $this;
    }

    public function getTimeMax(): ?DateTimeInterface
    {
        return $this->timeMax;
    }

    public function setTimeMax(?DateTimeInterface $timeMax): self
    {
        $this->timeMax = $timeMax;
        return $this;
    }

    public function getFullAddress(): string
    {
        return $this->fullAddress;
    }

    public function setFullAddress(string $fullAddress): self
    {
        $this->fullAddress = $fullAddress;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function setBuilding(?string $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function getApartmentOffice(): ?string
    {
        return $this->apartmentOffice;
    }

    public function setApartmentOffice(?string $apartmentOffice): self
    {
        $this->apartmentOffice = $apartmentOffice;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber(?string $trackingNumber): self
    {
        $this->trackingNumber = $trackingNumber;
        return $this;
    }

    public function getCarrierId(): ?int
    {
        return $this->carrierId;
    }

    public function setCarrierId(?int $carrierId): self
    {
        $this->carrierId = $carrierId;
        return $this;
    }

    public function getOffsetReason(): ?bool
    {
        return $this->offsetReason;
    }

    public function setOffsetReason(?bool $offsetReason): self
    {
        $this->offsetReason = $offsetReason;
        return $this;
    }

    public function getOffsetDate(): ?DateTimeInterface
    {
        return $this->offsetDate;
    }

    public function setOffsetDate(?DateTimeInterface $offsetDate): self
    {
        $this->offsetDate = $offsetDate;
        return $this;
    }

    public function getProposedDate(): ?DateTimeInterface
    {
        return $this->proposedDate;
    }

    public function setProposedDate(?DateTimeInterface $proposedDate): self
    {
        $this->proposedDate = $proposedDate;
        return $this;
    }

    public function getShipDate(): ?DateTimeInterface
    {
        return $this->shipDate;
    }

    public function setShipDate(?DateTimeInterface $shipDate): self
    {
        $this->shipDate = $shipDate;
        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function isDelayed(): bool
    {
        return $this->offsetDate !== null;
    }

    public function getDeliveryPeriod(): ?string
    {
        if ($this->timeMin && $this->timeMax) {
            return $this->timeMin->format('d.m.Y') . ' - ' . $this->timeMax->format('d.m.Y');
        }

        return null;
    }

    public function hasTracking(): bool
    {
        return $this->trackingNumber !== null && $this->trackingNumber !== '';
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }
}
