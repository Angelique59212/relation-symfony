<?php

namespace App\Controller;

use App\Entity\Reader;
use App\Repository\ReaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ReaderController extends AbstractController
{
    #[Route('/api/reader/', name: 'app_reader', methods: ['GET'])]
    public function getAll(ReaderRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $readerList = $repository->findAll();
        $jsonReader = $serializer->serialize($readerList, 'json', ['groups'=>"getReaders"]);

        return new JsonResponse($jsonReader, Response::HTTP_OK, [], true);
    }

    #[Route('/api/reader/add/', name: 'app_reader_add', methods: ['POST'])]
    public function addReader(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $reader = $serializer->deserialize($request->getContent(), Reader::class, 'json');
        $em->persist($reader);
        $em->flush();

        $jsonReader = $serializer->serialize($reader, 'json');
        return new JsonResponse($jsonReader, Response::HTTP_OK, [], true);
    }

    #[Route('/api/reader/{id}', name: 'app_reader_id', methods: ['GET'])]
    public function getById(int $id, ReaderRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $reader = $repository->find($id);
        if ($reader) {
            $jsonReader = $serializer->serialize($reader, 'json', ['groups' => 'getReaders']);
            return new JsonResponse($jsonReader, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(['message' => 'reader not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/reader/{id}', name: 'app_reader_delete', methods: ['DELETE'])]
    public function deleteReader(int $id, ReaderRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $reader = $repository->find($id);
        if ($reader) {
            $em->remove($reader);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(["message" => "Reader not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/reader/{id}', name: 'app_reader_update', methods: ['PUT'])]
    public function updateReader(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, Reader $currentReader = null): JsonResponse
    {
        if ($currentReader instanceof Reader) {
            $updateReader = $serializer->deserialize($request->getContent(), Reader::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentReader]);

            $em->persist($updateReader);
            $em->flush();
            return new JsonResponse(['message' => 'reader update'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'reader not found'], Response::HTTP_NOT_FOUND);
    }
}
