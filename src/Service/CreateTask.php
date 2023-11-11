<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use DateTime;
use DateTimeImmutable;
use Exception;

class CreateTask
{
    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    public function execute(array $data): array
    {
        $uuid = $this->taskRepository->getNextUuid();

        $date = new DateTime($data['dueDate'] . ' ' . $data['hour']);

        $this->ensureValidDueDate($date);
        $this->ensureNoDatetimeConflict($date);

        $task = new Task(
            null,
            $uuid,
            $data['type'],
            $data['title'],
            $data['description'],
            $data['dueDate'],
            $data['hour'],
            $data['done'] ?? false,
            new DateTimeImmutable(),
            null,
            null
        );

        $this->taskRepository->persist($task);

        return $task->toArray();
    }

    private function ensureValidDueDate(DateTime $date): void
    {
        $isInvalidDate = $date < new DateTime();

        if($isInvalidDate){
            throw new Exception("Não permitida a criação de tarefa com data e hora menor do que atual.", 400);
        }
    }

    private function ensureNoDatetimeConflict(DateTime $date): void
    {
        $isDatetimeConflict = $this->taskRepository->existsScheduledTaskAt($date);

        if($isDatetimeConflict){
            throw new Exception("Já existe uma tarefa no mesmo dia e horário.", 400);
        }
    }
}
