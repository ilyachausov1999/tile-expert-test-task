<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['order_detail'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    #[Groups(['order_detail'])]
    private string $hash;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['order_detail'])]
    private ?int $userId = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['order_detail'])]
    private int $managerId;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['order_detail'])]
    private int $statusId;

    #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
    #[Groups(['order_detail'])]
    private string $token;

    #[ORM\Column(type: Types::STRING, length: 15, unique: true, nullable: true)]
    #[Groups(['order_detail'])]
    private ?string $number = null;

    #[ORM\Column(type: Types::STRING, length: 200)]
    #[Groups(['order_detail'])]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['order_detail'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['order_detail'])]
    private int $payType;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    #[Groups(['order_detail'])]
    private ?string $discount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6, options: ['default' => '1.000000'])]
    #[Groups(['order_detail'])]
    private string $curRate = '1.000000';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['order_detail'])]
    private bool $specPrice = false;

    #[ORM\Column(type: Types::STRING, length: 5)]
    #[Groups(['order_detail'])]
    private string $locale;

    #[ORM\Column(type: Types::STRING, length: 3, options: ['default' => 'EUR'])]
    #[Groups(['order_detail'])]
    private string $currency = 'EUR';

    #[ORM\Column(type: Types::STRING, length: 3, options: ['default' => 'm'])]
    #[Groups(['order_detail'])]
    private string $measure = 'm';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, nullable: true)]
    #[Groups(['order_detail'])]
    private ?string $weightGross = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 1])]
    #[Groups(['order_detail'])]
    private int $step = 1;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => true])]
    #[Groups(['order_detail'])]
    private ?bool $addressEqual = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['order_detail'])]
    private bool $bankTransferRequested = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['order_detail'])]
    private bool $acceptPay = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['order_detail'])]
    private bool $productReview = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['order_detail'])]
    private bool $process = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['order_detail'])]
    private bool $showMsg = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['order_detail'])]
    private ?\DateTimeInterface $fullPaymentDate = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['order_detail'])]
    private ?int $mirror = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    #[Groups(['order_detail'])]
    private int $entranceReview = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['order_detail'])]
    private ?int $addressPayer = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['order_detail'])]
    private ?string $bankDetails = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['order_detail'])]
    private ?\DateTimeInterface $payDateExecution = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['order_detail'])]
    private ?\DateTimeInterface $factDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['order_detail'])]
    private ?\DateTimeInterface $sendingDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['order_detail'])]
    private ?\DateTimeInterface $canceledAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['order_detail'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['order_detail'])]
    private \DateTimeInterface $updatedAt;

    #[ORM\OneToMany(targetEntity: OrderArticle::class, mappedBy: 'order')]
    #[Groups(['order_articles'])]
    private Collection $orderArticles;

    #[ORM\OneToOne(targetEntity: OrderDelivery::class, mappedBy: 'order')]
    #[Groups(['order_delivery'])]
    private ?OrderDelivery $delivery = null;

    public function __construct()
    {
        $this->orderArticles = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getManagerId(): int
    {
        return $this->managerId;
    }

    public function setManagerId(int $managerId): self
    {
        $this->managerId = $managerId;
        return $this;
    }

    public function getStatusId(): int
    {
        return $this->statusId;
    }

    public function setStatusId(int $statusId): self
    {
        $this->statusId = $statusId;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getPayType(): int
    {
        return $this->payType;
    }

    public function setPayType(int $payType): self
    {
        $this->payType = $payType;
        return $this;
    }

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    public function getCurRate(): string
    {
        return $this->curRate;
    }

    public function setCurRate(string $curRate): self
    {
        $this->curRate = $curRate;
        return $this;
    }

    public function isSpecPrice(): bool
    {
        return $this->specPrice;
    }

    public function setSpecPrice(bool $specPrice): self
    {
        $this->specPrice = $specPrice;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getMeasure(): string
    {
        return $this->measure;
    }

    public function setMeasure(string $measure): self
    {
        $this->measure = $measure;
        return $this;
    }

    public function getWeightGross(): ?string
    {
        return $this->weightGross;
    }

    public function setWeightGross(?string $weightGross): self
    {
        $this->weightGross = $weightGross;
        return $this;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function setStep(int $step): self
    {
        $this->step = $step;
        return $this;
    }

    public function isAddressEqual(): ?bool
    {
        return $this->addressEqual;
    }

    public function setAddressEqual(?bool $addressEqual): self
    {
        $this->addressEqual = $addressEqual;
        return $this;
    }

    public function isBankTransferRequested(): bool
    {
        return $this->bankTransferRequested;
    }

    public function setBankTransferRequested(bool $bankTransferRequested): self
    {
        $this->bankTransferRequested = $bankTransferRequested;
        return $this;
    }

    public function isAcceptPay(): bool
    {
        return $this->acceptPay;
    }

    public function setAcceptPay(bool $acceptPay): self
    {
        $this->acceptPay = $acceptPay;
        return $this;
    }

    public function isProductReview(): bool
    {
        return $this->productReview;
    }

    public function setProductReview(bool $productReview): self
    {
        $this->productReview = $productReview;
        return $this;
    }

    public function isProcess(): bool
    {
        return $this->process;
    }

    public function setProcess(bool $process): self
    {
        $this->process = $process;
        return $this;
    }

    public function isShowMsg(): bool
    {
        return $this->showMsg;
    }

    public function setShowMsg(bool $showMsg): self
    {
        $this->showMsg = $showMsg;
        return $this;
    }

    public function getFullPaymentDate(): ?DateTimeInterface
    {
        return $this->fullPaymentDate;
    }

    public function setFullPaymentDate(?DateTimeInterface $fullPaymentDate): self
    {
        $this->fullPaymentDate = $fullPaymentDate;
        return $this;
    }

    public function getMirror(): ?int
    {
        return $this->mirror;
    }

    public function setMirror(?int $mirror): self
    {
        $this->mirror = $mirror;
        return $this;
    }

    public function getEntranceReview(): int
    {
        return $this->entranceReview;
    }

    public function setEntranceReview(int $entranceReview): self
    {
        $this->entranceReview = $entranceReview;
        return $this;
    }

    public function getAddressPayer(): ?int
    {
        return $this->addressPayer;
    }

    public function setAddressPayer(?int $addressPayer): self
    {
        $this->addressPayer = $addressPayer;
        return $this;
    }

    public function getBankDetails(): ?string
    {
        return $this->bankDetails;
    }

    public function setBankDetails(?string $bankDetails): self
    {
        $this->bankDetails = $bankDetails;
        return $this;
    }

    public function getPayDateExecution(): ?DateTimeInterface
    {
        return $this->payDateExecution;
    }

    public function setPayDateExecution(?DateTimeInterface $payDateExecution): self
    {
        $this->payDateExecution = $payDateExecution;
        return $this;
    }

    public function getFactDate(): ?DateTimeInterface
    {
        return $this->factDate;
    }

    public function setFactDate(?DateTimeInterface $factDate): self
    {
        $this->factDate = $factDate;
        return $this;
    }

    public function getSendingDate(): ?DateTimeInterface
    {
        return $this->sendingDate;
    }

    public function setSendingDate(?DateTimeInterface $sendingDate): self
    {
        $this->sendingDate = $sendingDate;
        return $this;
    }

    public function getCanceledAt(): ?DateTimeInterface
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(?DateTimeInterface $canceledAt): self
    {
        $this->canceledAt = $canceledAt;
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

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isCanceled(): bool
    {
        return $this->canceledAt !== null;
    }

    public function cancel(): self
    {
        $this->canceledAt = new DateTime();
        return $this;
    }

    public function markAsPaid(): self
    {
        $this->fullPaymentDate = new DateTime();
        return $this;
    }

    public function getOrderArticles(): Collection
    {
        return $this->orderArticles;
    }

    public function getDelivery(): ?OrderDelivery
    {
        return $this->delivery;
    }

    public function addOrderArticle(OrderArticle $orderArticle): self
    {
        if (!$this->orderArticles->contains($orderArticle)) {
            $this->orderArticles[] = $orderArticle;
            $orderArticle->setOrder($this);
        }

        return $this;
    }

    public function removeOrderArticle(OrderArticle $orderArticle): self
    {
        if ($this->orderArticles->removeElement($orderArticle)) {
            if ($orderArticle->getOrder() === $this) {
                $orderArticle->setOrder(null);
            }
        }

        return $this;
    }

    public function setDelivery(?OrderDelivery $delivery): self
    {
        if ($delivery === null && $this->delivery !== null) {
            $this->delivery->setOrder(null);
        }

        if ($delivery !== null && $delivery->getOrder() !== $this) {
            $delivery->setOrder($this);
        }

        $this->delivery = $delivery;

        return $this;
    }
}
