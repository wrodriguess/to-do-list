<?php

namespace App\Controller;

use App\Service\UpdateTask;
use DomainException;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PutTaskController extends AbstractController
{
    public function __construct(private readonly UpdateTask $updateTask)
    {
    }

    #[Route('/tasks/{id}', name: 'update_task', methods: ['PUT'])]
    public function handle(string $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Obriga todos os campos serem preenchidos, mesmo não utilizando o método notEmpty()
            // v::key('type', v::intType())
            //  ->key('title', v::stringType())
            //  ->key('description', v::stringType())
            //  ->key('dueDate', v::stringType())
            //  ->key('hour', v::stringType())
            //  ->assert($data);

            $task = $this->updateTask->execute($id, $data);
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
