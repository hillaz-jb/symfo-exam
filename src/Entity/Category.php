<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 150)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private string $description;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'id_parent')]
    private ?Category $parent;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $id_parent;

    #[Pure] public function __construct()
    {
        $this->id_parent = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getIdParent(): Collection
    {
        return $this->id_parent;
    }

    public function addIdParent(self $idParent): self
    {
        if (!$this->id_parent->contains($idParent)) {
            $this->id_parent[] = $idParent;
            $idParent->setParent($this);
        }

        return $this;
    }

    public function removeIdParent(self $idParent): self
    {
        if ($this->id_parent->removeElement($idParent)) {
            if ($idParent->getParent() === $this) {
                $idParent->setParent(null);
            }
        }

        return $this;
    }


}
