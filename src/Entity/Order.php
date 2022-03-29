<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stripe_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount_subtotal;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount_total;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount_tax;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $user;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $payment_intent;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStripeId(): ?string
    {
        return $this->stripe_id;
    }

    public function setStripeId(string $stripe_id): self
    {
        $this->stripe_id = $stripe_id;

        return $this;
    }

    public function getAmountSubtotal(): ?int
    {
        return $this->amount_subtotal;
    }

    public function setAmountSubtotal(int $amount_subtotal): self
    {
        $this->amount_subtotal = $amount_subtotal;

        return $this;
    }

    public function getAmountTotal(): ?int
    {
        return $this->amount_total;
    }

    public function setAmountTotal(int $amount_total): self
    {
        $this->amount_total = $amount_total;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getAmountTax(): ?int
    {
        return $this->amount_tax;
    }

    public function setAmountTax(int $amount_tax): self
    {
        $this->amount_tax = $amount_tax;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getPaymentIntent(): ?string
    {
        return $this->payment_intent;
    }

    public function setPaymentIntent(?string $payment_intent): self
    {
        $this->payment_intent = $payment_intent;

        return $this;
    }
}
