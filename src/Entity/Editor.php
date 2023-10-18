<?php

namespace App\Entity;

use App\Repository\EditorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EditorRepository::class)]
class Editor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getBooks", "getEditors", "getAuthors", "getReaders"])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups(["getBooks", "getEditors", "getAuthors", "getReaders"])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getBooks", "getEditors", "getAuthors", "getReaders"])]
    private ?string $address = null;

    #[ORM\Column(length: 20)]
    #[Groups(["getBooks", "getEditors", "getAuthors", "getReaders"])]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'editor', targetEntity: Book::class)]
    #[Groups("getEditors")]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

/**
 * @return Collection<int, Book>
 */
public function getBooks(): Collection
{
    return $this->books;
}

public function addBook(Book $book): static
{
    if (!$this->books->contains($book)) {
        $this->books->add($book);
        $book->setEditor($this);
    }

    return $this;
}

public function removeBook(Book $book): static
{
    if ($this->books->removeElement($book)) {
        // set the owning side to null (unless already changed)
        if ($book->getEditor() === $this) {
            $book->setEditor(null);
        }
    }

    return $this;
}
}
