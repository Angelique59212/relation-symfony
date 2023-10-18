<?php

namespace App\Entity;

use App\Repository\ReaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReaderRepository::class)]
class Reader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getReaders', 'getBooks'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getReaders', 'getBooks'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['getReaders', 'getBooks'])]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\ManyToMany(targetEntity: Book::class, inversedBy: 'readers')]
    #[Groups(['getReaders'])]
    private Collection $Books;

    public function __construct()
    {
        $this->Books = new ArrayCollection();
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

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->Books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->Books->contains($book)) {
            $this->Books->add($book);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        $this->Books->removeElement($book);

        return $this;
    }
}
