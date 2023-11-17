<?php

namespace App\Controller;

use App\Service\DoneTask;
use DomainException;
use Respect\Validation\Exceptions\NestedValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PutDoneTaskController extends AbstractController
{
    public function __construct(private readonly DoneTask $doneTask)
    {
    }

    #[Route('/tasks/{id}/{done}', name: 'done_talk', methods: ['PUT'])]
    public function handle(string $id, bool $done): JsonResponse
    {
        try {
            $output = $this->doneTask->execute($id, $done);
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

        return new JsonResponse($output, 200);
    }
}
