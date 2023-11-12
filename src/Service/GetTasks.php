<?php

namespace App\Service;

use App\Repository\TaskRepository;

class GetTasks
{
    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    public function execute(): array
    {
        $tasks = $this->taskRepository->getAll();

        $output = [];

        foreach ($tasks as $task) {
            $output[] = $task->toArray();
        }

        return $output;
    }
}
