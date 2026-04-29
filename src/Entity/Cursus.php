<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\BlameableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Cursus
 *
 * Represents a training cursus 
 * 
 * A cursus belongs to a theme and contains multiple lessons.
 */
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cursus implements BlameableInterface
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    /**
     * Cursus title
     */
    #[ORM\Column(length: 255)]
    private string $title;

    /**
     * Price of cursus
     */
    #[ORM\Column(type: 'integer')]
    private int $price; // en centimes

    /**
     * Theme relation
     */
    #[ORM\ManyToOne(targetEntity: Theme::class, inversedBy: 'cursus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theme $theme = null;

    /**
     * Lessons inside cursus
     */
    #[ORM\OneToMany(mappedBy: 'cursus', targetEntity: Lesson::class, cascade: ['persist', 'remove'])]
    private Collection $lessons;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }

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
     * Get theme
     */
    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    /**
     * Set theme
     */
    public function setTheme(?Theme $theme): self
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * Get lessons
     *
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    /**
     * Add lesson
     */
    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setCursus($this);
        }

        return $this;
    }

    /**
     * Remove lesson
     */
    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            if ($lesson->getCursus() === $this) {
                $lesson->setCursus(null);
            }
        }

        return $this;
    }
}