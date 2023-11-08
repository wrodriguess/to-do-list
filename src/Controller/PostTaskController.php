<?php

namespace App\Controller;

use App\Service\CreateTask;
use DomainException;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostTaskController extends AbstractController
{
    public function __construct(private readonly CreateTask $createTask)
    {
    }

    #[Route('/task', name: 'create_talk', methods: ['POST'])]
    public function handle(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            v::key('id', v::intType()->notEmpty())
                ->key('type', v::intType()->notEmpty())
                ->key('title', v::stringType()->notEmpty())
                ->key('description', v::stringType())
                ->key('dueDate', v::stringType()->notEmpty())
                ->key('hour', v::stringType()->notEmpty())
                ->assert($data);

            $task = $this->createTask->execute($data);
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

        return new JsonResponse($task, 201);
    }
}
