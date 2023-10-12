<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{
    #[Route('/api/author', name: 'app_author', methods: ['GET'])]
    public function getAll(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authorList = $authorRepository->findAll();
        $jsonAuthorList = $serializer->serialize($authorList, 'json',['groups'=>'getBooks']);

        return new JsonResponse($jsonAuthorList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/author/{id}', name: 'app_author_id', methods: ['GET'])]
    public function getById(int $id, AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $author = $authorRepository->find($id);
        if ($author) {
            $jsonAuthor = $serializer->serialize($author,'json', ['groups'=>'getBooks']);

            return new JsonResponse($jsonAuthor, Response::HTTP_OK, [], true);

        }
        return new JsonResponse(['message'=>'author not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/author/add', name: 'app_author_add', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $author = $serializer->deserialize($request->getContent(), Author::class, 'json', ['groups'=>'getBooks']);

        $em->persist($author);
        $em->flush();

        $jsonAuthor = $serializer->serialize($author, 'json');

        return new JsonResponse($jsonAuthor, Response::HTTP_OK, [], true);
    }

    #[Route('/api/author/{id}', name: 'app_author_delete', methods: ['DELETE'])]
    public function deleteAuthor(int $id, AuthorRepository $authorRepository, EntityManagerInterface $em):JsonResponse
    {
        $author = $authorRepository->find($id);
        if ($author) {
            $em->remove($author);
            $em->flush();

            return new JsonResponse(null,Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(["message"=>"Author not found"], Response::HTTP_NOT_FOUND);
    }
}
