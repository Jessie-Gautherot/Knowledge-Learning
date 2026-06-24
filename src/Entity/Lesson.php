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

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoUrl = null;

    /**
     * Price stored in cents.
     */
    #[ORM\Column(type: 'integer')]
    private int $price; 

    /**
     * Cursus associated with this lesson.
     */
    #[ORM\ManyToOne(targetEntity: Cursus::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cursus $cursus = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(?string $videoUrl): self
    {
        $this->videoUrl = $videoUrl;
        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    public function setCursus(?Cursus $cursus): self
    {
        $this->cursus = $cursus;
        return $this;
    }
}