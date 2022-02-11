<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TechniqueRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Entity for the differents techniques used for the paintings
 * Entité pour les différentes techniques utilisées pour les peintures
 * 
 * @ORM\Entity(repositoryClass=TechniqueRepository::class)
 * @UniqueEntity("type")
 * 
 * @ORM\HasLifecycleCallbacks()
 */
class Technique
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups("paintings_browse")
     * @Groups("techniques_browse")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * 
     * @Groups("paintings_browse")
     * @Groups("techniques_browse")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Painting::class, mappedBy="techniques")
     */
    private $paintings;

    public function __construct()
    {
        $this->paintings = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Function to update the updatedAt value automatically
     * 
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return Collection|Painting[]
     */
    public function getPaintings(): Collection
    {
        return $this->paintings;
    }

    public function addPainting(Painting $painting): self
    {
        if (!$this->paintings->contains($painting)) {
            $this->paintings[] = $painting;
            $painting->addTechniques($this);
        }

        return $this;
    }

    public function removePainting(Painting $painting): self
    {
        if ($this->paintings->removeElement($painting)) {
            $painting->removeTechniques($this);
        }

        return $this;
    }

    /**
     * @return Collection|Painting[]
     */
    public function getPainti(): Collection
    {
        return $this->painti;
    }

    public function addPainti(Painting $painti): self
    {
        if (!$this->painti->contains($painti)) {
            $this->painti[] = $painti;
            $painti->addTechnique($this);
        }

        return $this;
    }

    public function removePainti(Painting $painti): self
    {
        if ($this->painti->removeElement($painti)) {
            $painti->removeTechnique($this);
        }

        return $this;
    }
}
