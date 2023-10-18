<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getBooks", "getAuthors", "getEditors", "getReaders"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getBooks", "getAuthors", "getEditors","getReaders"])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(["getBooks", "getAuthors", "getEditors","getReaders"])]
    private ?int $years = null;

    #[ORM\Column]
    #[Groups(["getBooks", "getAuthors", "getEditors","getReaders"])]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["getBooks", "getAuthors", "getEditors","getReaders"])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getBooks","getEditors", "getReaders"])]
    private ?Author $author = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[Groups(["getBooks", "getAuthors", "getReaders"])]
    private ?Editor $editor = null;

    #[ORM\ManyToMany(targetEntity: Reader::class, mappedBy: 'Books')]
    #[Groups(['getBooks'])]
    private Collection $readers;

    public function __construct()
    {
        $this->readers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getYears(): ?int
    {
        return $this->years;
    }

    public function setYears(int $years): static
    {
        $this->years = $years;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }


    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @return Collection<int, Reader>
     */
    public function getReaders(): Collection
    {
        return $this->readers;
    }

    public function addReader(Reader $reader): static
    {
        if (!$this->readers->contains($reader)) {
            $this->readers->add($reader);
            $reader->addBook($this);
        }

        return $this;
    }

    public function removeReader(Reader $reader): static
    {
        if ($this->readers->removeElement($reader)) {
            $reader->removeBook($this);
        }

        return $this;
    }
}
