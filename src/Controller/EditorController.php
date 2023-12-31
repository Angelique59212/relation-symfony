<?php

namespace App\Controller;

use App\Entity\Editor;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class EditorController extends AbstractController
{
    #[Route('/api/editor', name: 'app_editor', methods: ['GET'])]
    public function getAll(EditorRepository $editorRepository, SerializerInterface $serializer): JsonResponse
    {
        $editorList = $editorRepository->findAll();
        $jsonEditorList = $serializer->serialize($editorList, 'json',['groups'=>'getEditors']);
        return new JsonResponse($jsonEditorList, Response::HTTP_OK, [],true);
    }

    #[Route('/api/editor/{id}', name: 'app_editor_id', methods: ['GET'])]
    public function getById(int $id, EditorRepository $editorRepository, SerializerInterface $serializer):JsonResponse
    {
        $editor = $editorRepository->find($id);
        if ($editor) {
            $jsonEditor = $serializer->serialize($editor, 'json',['groups'=>'getEditors']);
            return new JsonResponse($jsonEditor,Response::HTTP_OK, [], true);
        }
        return new JsonResponse(['message'=>'editor not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/editor/add', name: 'app_editor_add', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer):JsonResponse
    {
        $editor = $serializer->deserialize($request->getContent(), Editor::class, 'json');

        $em->persist($editor);
        $em->flush();

        $jsonEditor = $serializer->serialize($editor, 'json');

        return new JsonResponse($jsonEditor, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/editor/{id}', name: 'app_editor_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em, SerializerInterface $serializer, EditorRepository $repository):JsonResponse
    {
        $editor = $repository->find($id);
        if ($editor) {
            $updateEditor = $serializer->deserialize($request->getContent(), Editor::class, "json");
            $editor->setPhone($updateEditor->getPhone());
            $editor->setAddress($updateEditor->getAddress());
            $editor->setName($updateEditor->getName());
            $em->flush($updateEditor);

            return new JsonResponse(["message"=>"editor updated", Response::HTTP_OK]);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND, [], true);
    }

    #[Route('/api/editor/{id}', name: 'app_editor_delete', methods: ['DELETE'])]
    public function deleteEditor(int $id, EditorRepository $editorRepository, EntityManagerInterface $em):JsonResponse
    {
        $editor = $editorRepository->find($id);
        if ($editor) {
            $em->remove($editor);
            $em->flush();

            return new JsonResponse(null,Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(["message"=>"Editor not found"], Response::HTTP_NOT_FOUND);
    }
}
