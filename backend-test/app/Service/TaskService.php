<?php

namespace RidwanHidayat\BackendTest\Service;

use RidwanHidayat\BackendTest\Domain\Task;
use RidwanHidayat\BackendTest\Exception\ValidationException;
use RidwanHidayat\BackendTest\Model\TaskRequest;
use RidwanHidayat\BackendTest\Model\TaskResponse;
use RidwanHidayat\BackendTest\Repository\TaskRepository;
use Firebase\JWT\JWT;
use RidwanHidayat\BackendTest\Helper\Helper;

class TaskService
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function token(string $username, string $password): ?string
    {
        $key = Helper::$key;
        $response = $this->taskRepository->token($username, $password);
        $payload = [
            'username' => $response['username'],
            'password' => $response['password'],
        ];
        return JWT::encode($payload, $key, 'HS256');
    }

    private function validateTaskRequest(TaskRequest $request): void
    {
        if (!isset($request->title)) {
            throw new ValidationException('Invalid arguments');
        } else if ($request->title == null || trim($request->title) == '') {
            throw new ValidationException('Title do not blank');
        }
    }

    public function save(TaskRequest $request): TaskResponse
    {
        $this->validateTaskRequest($request);

        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;

        $result = $this->taskRepository->save($task);

        $response = new TaskResponse();
        $response->task = $result;

        return $response;
    }

    public function findAll(): array
    {
        return $this->taskRepository->findAll();
    }

    public function findById(int $id): ?Task
    {
        return $this->taskRepository->findById($id);
    }

    public function update(TaskRequest $request): TaskResponse
    {
        $this->validateTaskRequest($request);

        $check = $this->taskRepository->findById($request->id);
        if ($check == null) {
            throw new ValidationException('Task not found!');
        }

        $task = new Task();
        $task->id = $request->id;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->isDone = $request->isDone;

        $this->taskRepository->update($task);

        $response = new TaskResponse();
        $response->task = $task;

        return $response;
    }

    public function delete(int $id): int
    {
        return $this->taskRepository->delete($id);
    }
}