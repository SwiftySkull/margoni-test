<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PaintingRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity describing all the elements we need for a painting
 * Entité qui décrit tous les éléments nécessaires pour une peinture
 * 
 * @ORM\Entity(repositoryClass=PaintingRepository::class)
 * 
 * @ORM\HasLifecycleCallbacks()
 */
class Painting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups("paintings_browse")
     * @Groups("one_from_categ")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * 
     * @Groups("paintings_browse")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=50)
     * 
     * @Groups("paintings_browse")
     */
    private $dbName;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     * 
     * @Groups("paintings_browse")
     */
    private $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Groups("paintings_browse")
     */
    private $height;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Groups("paintings_browse")
     */
    private $width;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Groups("painting_read")
     */
    private $location;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Groups("painting_read")
     */
    private $information;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Frame::class, inversedBy="paintings")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * 
     * @Groups("painting_read")
     */
    private $frame;

    /**
     * @ORM\ManyToOne(targetEntity=Size::class, inversedBy="paintings")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * 
     * @Groups("paintings_browse")
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity=Situation::class, inversedBy="paintings")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * 
     * @Groups("painting_read")
     */
    private $situation;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="paintings")
     * 
     * @Groups("paintings_browse")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity=Technique::class, inversedBy="paintings")
     * 
     * @Groups("paintings_browse")
     */
    private $techniques;

    /**
     * @ORM\OneToOne(targetEntity=Picture::class, cascade={"persist", "remove"})
     * 
     * @Groups("paintings_browse")
     * @Groups("painting_read")
     */
    private $picture;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $webDisplay;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->techniques = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(?int $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;

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

    public function getFrame(): ?Frame
    {
        return $this->frame;
    }

    public function setFrame(?Frame $frame): self
    {
        $this->frame = $frame;

        return $this;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function setSize(?Size $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSituation(): ?Situation
    {
        return $this->situation;
    }

    public function setSituation(?Situation $situation): self
    {
        $this->situation = $situation;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategories(Category $categories): self
    {
        if (!$this->categories->contains($categories)) {
            $this->categories[] = $categories;
        }

        return $this;
    }

    public function removeCategories(Category $categories): self
    {
        $this->categories->removeElement($categories);

        return $this;
    }

    /**
     * @return Collection|Techniques[]
     */
    public function getTechnique(): Collection
    {
        return $this->techniques;
    }

    public function addTechniques(Technique $techniques): self
    {
        if (!$this->techniques->contains($techniques)) {
            $this->techniques[] = $techniques;
        }

        return $this;
    }

    public function removeTechniques(Technique $techniques): self
    {
        $this->techniques->removeElement($techniques);

        return $this;
    }

    /**
     * Get the value of dbName
     */ 
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * Set the value of dbName
     *
     * @return  self
     */ 
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection|Technique[]
     */
    public function getTechniques(): Collection
    {
        return $this->techniques;
    }

    public function addTechnique(Technique $technique): self
    {
        if (!$this->techniques->contains($technique)) {
            $this->techniques[] = $technique;
        }

        return $this;
    }

    public function removeTechnique(Technique $technique): self
    {
        $this->techniques->removeElement($technique);

        return $this;
    }

    public function getWebDisplay(): ?bool
    {
        return $this->webDisplay;
    }

    public function setWebDisplay(?bool $webDisplay): self
    {
        $this->webDisplay = $webDisplay;

        return $this;
    }
}
