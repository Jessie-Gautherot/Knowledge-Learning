<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\BlameableTrait;

/**
 * Class Purchase
 *
 * Represents a purchase made by a user.
 *
 * A purchase can be associated with a cursus
 * or with a lesson 
 */
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Purchase implements BlameableInterface
{
    use TimestampableTrait;
    use BlameableTrait;

    public const TYPE_CURSUS = 'cursus';
    public const TYPE_LESSON = 'lesson';
    
    public const STATUS_SUCCESS = 'success';
    public const STATUS_PENDING = 'pending';
    public const STATUS_FAILED = 'failed';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    /**
     * Buyer
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /**
     * Type of purchase (cursus or lesson)
     */
    #[ORM\Column(length: 20)]
    private string $type;

    /**
     * Purchased cursus (nullable)
     */
    #[ORM\ManyToOne(targetEntity: Cursus::class)]
    private ?Cursus $cursus = null;

    /**
     * Purchased lesson (nullable)
     */
    #[ORM\ManyToOne(targetEntity: Lesson::class)]
    private ?Lesson $lesson = null;

    /**
     * Price in cents
     */
    #[ORM\Column(type: 'integer')]
    private int $price;

    /**
     * Payment status (success, pending, failed)
     */
    #[ORM\Column(length: 50)]
    private string $status;

    /**
     * Payment provider ID 
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentId = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
    * Set purchase type.
    *
    * Only cursus and lesson types are allowed.
    *
    * @throws \InvalidArgumentException If the type is invalid
    */
    public function setType(string $type): self
    {
        if (!in_array($type, [self::TYPE_CURSUS, self::TYPE_LESSON])) {
            throw new \InvalidArgumentException('Invalid purchase type');
        }

        $this->type = $type;
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

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function setPaymentId(?string $paymentId): self
    {
        $this->paymentId = $paymentId;
        return $this;
    }
}