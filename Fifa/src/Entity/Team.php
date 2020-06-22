<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", length=255)
     */
    private $scoring;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\Column(type="boolean")
     */
    private $my_team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Continent", inversedBy="teams")
     */
    private $continent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getScoring(): ?int
    {
        return $this->scoring;
    }

    public function setScoring(int $scoring): self
    {
        $this->scoring = $scoring;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getMyTeam(): ?bool
    {
        return $this->my_team;
    }

    public function setMyTeam(bool $my_team): self
    {
        $this->my_team = $my_team;

        return $this;
    }

    public function getContinent(): ?continent
    {
        return $this->continent;
    }

    public function setContinent(?continent $continent): self
    {
        $this->continent = $continent;

        return $this;
    }
}
