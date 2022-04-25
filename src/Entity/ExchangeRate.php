<?php

namespace App\Entity;

use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=ExchangeRateRepository::class)
 */
class ExchangeRate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Currency
     * @ORM\Column(type="string", length=255)
     */
    private $startingCurrency;

    /**
     * @Assert\Currency
     * @ORM\Column(type="string", length=255)
     */
    private $finalCurrency;

    /**
     
     * @Assert\Positive(message="proszę wpisać więcej niż 0")
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @Assert\Date
     * @var string A "d-m-Y" formatted value
     * @ORM\Column(type="date", nullable=true)
     */
    private $startDate;

    /**
     * @Assert\Date
     * @var string A "d-m-Y" formatted value
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartingCurrency(): ?string
    {
        return $this->startingCurrency;
    }

    public function setStartingCurrency(string $startingCurrency): self
    {
        $this->startingCurrency = $startingCurrency;

        return $this;
    }

    public function getFinalCurrency(): ?string
    {
        return $this->finalCurrency;
    }

    public function setFinalCurrency(string $finalCurrency): self
    {
        $this->finalCurrency = $finalCurrency;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }
    /**
    * @Assert\Callback
    */
    public function validate(ExecutionContextInterface $context, $payload) {
        if ($this->startDate > $this->endDate) {
            $context->buildViolation('Data startowa musi być wcześniejsza niż końcowa.')
                ->atPath('startDate')
                ->addViolation();
        }
        
        $today = date("Y-m-d");
        $today_time = strtotime($today);
        if ($this->startDate !== null && ($this->startDate->getTimestamp() >  $today_time || $this->endDate->getTimestamp() >  $today_time )) {
            $context->buildViolation('Data startowa lub końcowa nie może większa od dnia dzisiejszego.')
                ->atPath('startDate')
                ->addViolation();
        }
    }
    
}
