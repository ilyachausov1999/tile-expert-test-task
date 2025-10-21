<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders_article')]
#[ORM\Index(name: 'idx_orders_article_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_orders_article_article_id', columns: ['article_id'])]
#[ORM\Index(name: 'idx_orders_article_order_article', columns: ['order_id', 'article_id'])]
#[ORM\Index(name: 'idx_orders_article_prices', columns: ['price'])]
#[ORM\HasLifecycleCallbacks]
class OrderArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $orderId;

    #[ORM\Column(type: Types::INTEGER)]
    private int $articleId;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3)]
    private string $amount;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $price;

    #[ORM\Column(type: Types::STRING, length: 3, nullable: true)]
    private ?string $displayMeasure = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6, options: ['default' => '1.000000'])]
    private string $conversionRate = '1.000000';

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    private ?string $weight = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, nullable: true)]
    private ?string $weightTotal = null;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    private ?string $specialNotes = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $updatedAt;

    // Связи (раскомментируйте когда создадите сущности)
    /*
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderArticles')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Article::class)]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id')]
    private Article $article;
    */

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

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
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
}
