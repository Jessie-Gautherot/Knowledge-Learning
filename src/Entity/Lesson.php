<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\BlameableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Lesson
 *
 * Represents a single lesson
 * 
 * A lesson belongs to a cursus
 */
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Lesson implements BlameableInterface
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    /**
     * Lesson title
     */
    #[ORM\Column(length: 255)]
    private string $title;

    /**
     * Lesson content
     */
    #[ORM\Column(type: 'text')]
    private string $content;

    /**
     * Video URL
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoUrl = null;

    /**
     * Price
     */
    #[ORM\Column(type: 'integer')]
    private int $price; // en centimes

    /**
     * Relation to cursus
     */
    #[ORM\ManyToOne(targetEntity: Cursus::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cursus $cursus = null;

    /**
     * Get ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set content
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get video URL
     */
    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    /**
     * Set video URL
     */
    public function setVideoUrl(?string $videoUrl): self
    {
        $this->videoUrl = $videoUrl;
        return $this;
    }

    /**
     * Get price
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * Set price
     */
    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get cursus
     */
    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    /**
     * Set cursus
     */
    public function setCursus(?Cursus $cursus): self
    {
        $this->cursus = $cursus;
        return $this;
    }
}