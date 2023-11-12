<?php

namespace App\Service;

use App\Repository\TaskRepository;

class GetTaskById
{
    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    public function execute(string $id): array
    {
        return $this->taskRepository->getById($id);
    }
}
