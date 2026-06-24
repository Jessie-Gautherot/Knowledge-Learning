<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\BlameableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Theme
 *
 * Represents a training theme
 * 
 * A theme groups multiple cursus with their lessons.
 */
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Theme implements BlameableInterface
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    /**
     * One theme has many cursus
     */
    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: Cursus::class, cascade: ['persist', 'remove'])]
    private Collection $cursus;

    public function __construct()
    {
        $this->cursus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * Return all cursus of this theme
     *
     * @return Collection<int, Cursus>
     */
    public function getCursus(): Collection
    {
        return $this->cursus;
    }

    /**
     * Add cursus to theme
     */
    public function addCursus(Cursus $cursus): self
    {
        if (!$this->cursus->contains($cursus)) {
            $this->cursus[] = $cursus;
            $cursus->setTheme($this);
        }

        return $this;
    }

    /**
     * Remove cursus from theme
     */
    public function removeCursus(Cursus $cursus): self
    {
        if ($this->cursus->removeElement($cursus)) {
            if ($cursus->getTheme() === $this) {
                $cursus->setTheme(null);
            }
        }

        return $this;
    }
}