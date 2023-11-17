<?php

namespace App\Service;

use App\Repository\TaskRepository;

class DeleteTask
{
    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    public function execute(string $id): void
    {
        $this->taskRepository->deleteById($id);
    }
}
