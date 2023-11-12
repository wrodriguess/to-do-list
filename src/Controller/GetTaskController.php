<?php

namespace App\Controller;

use App\Service\GetTaskById;
use DomainException;
use Respect\Validation\Exceptions\NestedValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetTaskController extends AbstractController
{
    public function __construct(private readonly GetTaskById $getTaskById)
    {
    }

    #[Route('/tasks/{id}', name: 'get_talk', methods: ['GET'])]
    public function handle(string $id): JsonResponse
    {
        try {
            $task = $this->getTaskById->execute($id);
        } catch (DomainException $e) {
            $payload = [
                'status' => 'error',
                'type' => $e->getType(),
                'message' => $e->getMessage(),
            ];

            return new JsonResponse($payload, 400);
        } catch (NestedValidationException $e) {
            $payload = [
                'status' => 'error',
                'type' => 'ValidationError',
                'message' => 'Houve um erro ao validar o corpo da requisição',
                'erros' => [$e->getMessages()]
            ];

            return new JsonResponse($payload, 422);
        }

        return new JsonResponse($task, 200);
    }
}
