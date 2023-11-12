<?php

namespace App\Controller;

use App\Service\GetTasks;
use DomainException;
use Respect\Validation\Exceptions\NestedValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetTasksController extends AbstractController
{
    public function __construct(private readonly GetTasks $getTasks)
    {
    }

    #[Route('/tasks', name: 'get_talks', methods: ['GET'])]
    public function handle(): JsonResponse
    {
        try {
            $tasks = $this->getTasks->execute();
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

        return new JsonResponse($tasks, 200);
    }
}
