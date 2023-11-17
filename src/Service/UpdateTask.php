<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use DateTime;
use DateTimeImmutable;
use Exception;

class UpdateTask
{
    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    public function execute(string $id, array $data): array
    {
        $taskData = $this->taskRepository->getById($id);

        if (!$taskData) {
            throw new Exception("Não existe task com id $id", 404);
        }

        $task = $this->taskRepository->find($taskData['id']);

        if (isset($data['type'])) {
            $task->setType($data['type']);
        }

        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }

        if (isset($data['dueDate'])) {
            $task->setDueDate($data['dueDate']);
        }

        if (isset($data['hour'])) {
            $task->setHour($data['hour']);
        }

        if (isset($data['done'])) {
            $task->setDone($data['done']);
        }

        $task->setUpdatedAt(new DateTimeImmutable());

        $this->taskRepository->persist($task);

        return $task->toArray();
    }
}
