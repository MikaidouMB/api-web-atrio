<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ApiResource]

class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\LessThanOrEqual(value: 'today - 150 years', message: 'La personne doit avoir moins de 150 ans.')]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Job::class)]
    private Collection $job;


    #[Groups(['read_people'])]
    public function getAge(): ?int
    {
        if ($this->birthdate === null) {
            return null;
        }

        $now = new \DateTime();
        $interval = $this->birthdate->diff($now);

        return $interval->y;
    }

    #[Groups(['read_people'])]
    public function getCurrentJobs(): Collection
    {
        $currentJobs = new ArrayCollection();

        $now = new \DateTime();

        foreach ($this->job as $job) {
            $jobEndDate = $job->getEndDate();

            if ($jobEndDate === null || $jobEndDate > $now) {
                $currentJobs->add($job);
            }
        }

        return $currentJobs;
    }

    public function __construct()
    {
        $this->job = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJob(): Collection
    {
        return $this->job;
    }

    public function addJob(Job $job): static
    {
        if (!$this->job->contains($job)) {
            $this->job->add($job);
            $job->setPerson($this);
        }

        return $this;
    }

    public function removeJob(Job $job): static
    {
        if ($this->job->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getPerson() === $this) {
                $job->setPerson(null);
            }
        }

        return $this;
    }
}
