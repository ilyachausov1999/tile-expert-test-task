<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
#[ORM\Index(name: 'idx_articles_sku', columns: ['sku'])]
#[ORM\Index(name: 'idx_articles_name', columns: ['name'])]
#[ORM\Index(name: 'idx_articles_factory', columns: ['factory'])]
#[ORM\Index(name: 'idx_articles_collection', columns: ['collection'])]
#[ORM\Index(name: 'idx_articles_base_price', columns: ['base_price'])]
#[ORM\HasLifecycleCallbacks]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    private string $sku;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $factory;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $collection;

    #[ORM\Column(type: Types::INTEGER)]
    private int $pallet;

    #[ORM\Column(type: Types::INTEGER)]
    private int $packaging;

    #[ORM\Column(type: Types::INTEGER)]
    private int $packagingCount;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $multiplePallet = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => false])]
    private ?bool $isSwimmingPool = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $basePrice;

    #[ORM\Column(type: Types::STRING, length: 3, options: ['default' => 'pcs'])]
    private string $baseMeasure = 'pcs';

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    private string $weight;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => true])]
    private ?bool $isActive = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $updatedAt;

    // Связи (раскомментируйте когда нужно)
    /*
    #[ORM\OneToMany(targetEntity: OrderArticle::class, mappedBy: 'article')]
    private Collection $orderArticles;
    */

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        // $this->orderArticles = new ArrayCollection();
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

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;
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

    public function getFactory(): string
    {
        return $this->factory;
    }

    public function setFactory(string $factory): self
    {
        $this->factory = $factory;
        return $this;
    }

    public function getCollection(): string
    {
        return $this->collection;
    }

    public function setCollection(string $collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    public function getPallet(): int
    {
        return $this->pallet;
    }

    public function setPallet(int $pallet): self
    {
        $this->pallet = $pallet;
        return $this;
    }

    public function getPackaging(): int
    {
        return $this->packaging;
    }

    public function setPackaging(int $packaging): self
    {
        $this->packaging = $packaging;
        return $this;
    }

    public function getPackagingCount(): int
    {
        return $this->packagingCount;
    }

    public function setPackagingCount(int $packagingCount): self
    {
        $this->packagingCount = $packagingCount;
        return $this;
    }

    public function getMultiplePallet(): ?bool
    {
        return $this->multiplePallet;
    }

    public function setMultiplePallet(?bool $multiplePallet): self
    {
        $this->multiplePallet = $multiplePallet;
        return $this;
    }

    public function isSwimmingPool(): ?bool
    {
        return $this->isSwimmingPool;
    }

    public function setIsSwimmingPool(?bool $isSwimmingPool): self
    {
        $this->isSwimmingPool = $isSwimmingPool;
        return $this;
    }

    public function getBasePrice(): string
    {
        return $this->basePrice;
    }

    public function setBasePrice(string $basePrice): self
    {
        $this->basePrice = $basePrice;
        return $this;
    }

    public function getBaseMeasure(): string
    {
        return $this->baseMeasure;
    }

    public function setBaseMeasure(string $baseMeasure): self
    {
        $this->baseMeasure = $baseMeasure;
        return $this;
    }

    public function getWeight(): string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;
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

    public function getPackagesPerPallet(): int
    {
        if ($this->packaging > 0) {
            return (int) ceil($this->pallet / $this->packaging);
        }

        return 0;
    }
}
