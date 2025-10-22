<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'orders_article')]
class OrderArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['order_articles'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['order_articles'])]
    private int $articleId;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3)]
    #[Groups(['order_articles'])]
    private string $amount;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['order_articles'])]
    private string $price;

    #[ORM\Column(type: Types::STRING, length: 3, nullable: true)]
    #[Groups(['order_articles'])]
    private ?string $displayMeasure = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6, options: ['default' => '1.000000'])]
    #[Groups(['order_articles'])]
    private string $conversionRate = '1.000000';

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    #[Groups(['order_articles'])]
    private ?string $weight = "0.0";

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, nullable: true)]
    #[Groups(['order_articles'])]
    private ?string $weightTotal = null;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    #[Groups(['order_articles'])]
    private ?string $specialNotes = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['order_articles'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['order_articles'])]
    private \DateTimeInterface $updatedAt;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderArticles')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
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

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateWeightTotal(): void
    {
        if ($this->amount && $this->weight) {
            $this->weightTotal = (string) round($this->amount * $this->weight, 3);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): self
    {
        $this->articleId = $articleId;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        $this->calculateWeightTotal();
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getDisplayMeasure(): ?string
    {
        return $this->displayMeasure;
    }

    public function setDisplayMeasure(?string $displayMeasure): self
    {
        $this->displayMeasure = $displayMeasure;
        return $this;
    }

    public function getConversionRate(): string
    {
        return $this->conversionRate;
    }

    public function setConversionRate(string $conversionRate): self
    {
        $this->conversionRate = $conversionRate;
        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;
        $this->calculateWeightTotal();
        return $this;
    }

    public function getWeightTotal(): ?string
    {
        return $this->weightTotal;
    }

    public function setWeightTotal(?string $weightTotal): self
    {
        $this->weightTotal = $weightTotal;
        return $this;
    }

    public function getSpecialNotes(): ?string
    {
        return $this->specialNotes;
    }

    public function setSpecialNotes(?string $specialNotes): self
    {
        $this->specialNotes = $specialNotes;
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
