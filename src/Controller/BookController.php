<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Editor;
use App\Entity\Reader;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\EditorRepository;
use App\Repository\ReaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    #[Route('/api/book', name: 'app_book', methods: ['GET'])]
    public function getAll(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $books = $bookRepository->findAll();
        $jsonBooks = $serializer->serialize($books, 'json', ['groups' => 'getBooks']);

        return new JsonResponse($jsonBooks, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'app_book_id', methods: ['GET'])]
    public function getById(int $id, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $book = $bookRepository->find($id);
        if ($book) {
            $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
            return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(['message' => 'book not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/book/{id}', name: 'app_book_delete', methods: ['DELETE'])]
    public function deleteBook(int $id, BookRepository $bookRepository, EntityManagerInterface $em): JsonResponse
    {
        $book = $bookRepository->find($id);
        if ($book) {
            $em->remove($book);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(["message" => "Book not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route(path: '/api/book/', name: 'app_book_add', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, AuthorRepository $authorRepository, EditorRepository $editorRepository): JsonResponse
    {
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');
        $content = $request->toArray();
        $authorId = $content['idAuthor'] ?? -1; // si c pas present on donne -1
        $author = $authorRepository->find($authorId);
        $editorId = $content['idEditor'] ?? -1;
        $editor = $editorRepository->find($editorId);
        if ($author && $editor) {
            $book->setAuthor($author);
            $book->setEditor($editor);
            $entityManager->persist($book);
            $entityManager->flush();
            return new JsonResponse($serializer->serialize($book, 'json', ['groups' => 'getBooks']), Response::HTTP_CREATED, [], true);
        }
        return new JsonResponse(['message' => "Author not found"], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/book/{id}', name: 'app_book_update', methods: ['PUT'])]
    public function updtateBook(Request $request, SerializerInterface $serializer, Book $currentBook, EntityManagerInterface $em): JsonResponse
    {
        $updateBook = $serializer->deserialize($request->getContent(),
            Book::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]
        );
        $em->persist($updateBook);
        $em->flush();
        return new JsonResponse(["message" => "Book update"], Response::HTTP_OK);
    }

    #[Route('/api/book/searchYear/{year}', name: 'app_book_searchYear', methods: ['GET'])]
    public function searchYear(int $year, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $books = $bookRepository->findAllGreaterThanYears($year);
        $jsonBooks = $serializer->serialize($books, 'json', ['groups' => 'getBooks']);

        return new JsonResponse($jsonBooks, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/searchLowerYear/{year}', name: 'app_book_searchLowerYear', methods: ['GET'])]
    public function searchLowerYear(int $year, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $books = $bookRepository->findAllLowerThanYear($year);
        $jsonBooks = $serializer->serialize($books, 'json', ['groups' => 'getBooks']);

        return new JsonResponse($jsonBooks, Response::HTTP_OK, [], true);
    }

    #[Route('/api/readerAdd/', name: 'app_add_reader', methods: ['PUT'])]
    public function addReader(Request $request,ReaderRepository $repository,EntityManagerInterface$em,SerializerInterface $serializer, BookRepository $bookRepository): JsonResponse
    {
        $content = $request->toArray();
        $bookId = $content['idBook'] ?? -1;
        $readerId = $content['idReader'] ?? -1;
        $book = $bookRepository->find($bookId);
        $reader = $repository->find($readerId);

        if ($book && $reader) {
            $book->addReader($reader);
            $em->persist($book);
            $em->flush();
            $jsonBook = $serializer->serialize($book, 'json', ['groups'=>'getBooks']);

            return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(['message'=>'book not found'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/readerDelete/', name: 'app_delete_reader', methods: ['PUT'])]
    public function deleteReader(Request $request,ReaderRepository $repository,EntityManagerInterface$em,SerializerInterface $serializer, BookRepository $bookRepository): JsonResponse
    {
        $content = $request->toArray();
        $bookId = $content['idBook'] ?? -1;
        $readerId = $content['idReader'] ?? -1;
        $book = $bookRepository->find($bookId);
        $reader = $repository->find($readerId);

        if ($book && $reader) {
            $book->removeReader($reader);
            $em->persist($book);
            $em->flush();
            $jsonBook = $serializer->serialize($book, 'json', ['groups'=>'getBooks']);

            return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(['message'=>'book not found'], Response::HTTP_BAD_REQUEST);
    }

}
