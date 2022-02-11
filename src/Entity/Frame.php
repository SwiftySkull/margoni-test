<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FrameRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Entity for the differents types of framework of the paintings
 * Entité pour les différents encadrements des peintures
 * 
 * @ORM\Entity(repositoryClass=FrameRepository::class)
 * @UniqueEntity("framing")
 * 
 * @ORM\HasLifecycleCallbacks()
 */
class Frame
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups("painting_read")
     * @Groups("frames_browse")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * 
     * @Groups("painting_read")
     * @Groups("frames_browse")
     */
    private $framing;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Painting::class, mappedBy="frame")
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

    public function getFraming(): ?string
    {
        return $this->framing;
    }

    public function setFraming(string $framing): self
    {
        $this->framing = $framing;

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
            $painting->setFrame($this);
        }

        return $this;
    }

    public function removePainting(Painting $painting): self
    {
        if ($this->paintings->removeElement($painting)) {
            // set the owning side to null (unless already changed)
            if ($painting->getFrame() === $this) {
                $painting->setFrame(null);
            }
        }

        return $this;
    }
}
