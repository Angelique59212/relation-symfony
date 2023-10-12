<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Editor;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    #[Route('/api/book', name: 'app_book', methods: ['GET'])]
    public function getAll(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $books = $bookRepository->findAll();
        $jsonBooks = $serializer->serialize($books, 'json', ['groups'=>'getBooks']);
        return new JsonResponse($jsonBooks, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'app_book_id', methods: ['GET'])]
    public function getById(int $id, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $book = $bookRepository->find($id);
        if ($book) {
            $jsonBook = $serializer->serialize($book, 'json',['groups'=>'getBooks']);
            return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(['message'=>'book not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/book/{id}', name: 'app_book_delete', methods: ['DELETE'])]
    public function deleteBook(int $id, BookRepository $bookRepository, EntityManagerInterface $em):JsonResponse
    {
        $book = $bookRepository->find($id);
        if ($book) {
            $em->remove($book);
            $em->flush();

            return new JsonResponse(null,Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(["message"=>"Book not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/book/create', name: 'app_book_create', methods: ['POST'])]
    public function createBook(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $author = $serializer->deserialize($request->getContent(), Author::class, 'json',['groups'=>'getBooks']);
        $book = $serializer->deserialize($request->getContent(), Book::class,'json',['groups'=>'getBooks']);
        $editor = $serializer->deserialize($request->getContent(), Editor::class, 'json',['groups'=>'getBooks']);

        $book = new Book();
        $book->setTitle($book['title']);
        $book->setYears($book['years']);
        $book->setPrice($book['price']);
        $book->setDescription($book['description']);
        $book->setAuthor($author);

        $em->persist($author);
        $em->persist($book);
        $em->flush();

        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);

        return new JsonResponse($jsonBook, Response::HTTP_CREATED, [], true);
    }
}
