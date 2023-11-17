<?php

namespace App\Service;

use App\Repository\TaskRepository;

class DoneTask
{
    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    public function execute(string $id, bool $done): array
    {
        return $this->taskRepository->doneTask($id, $done);
    }
}
