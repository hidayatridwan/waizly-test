<?php

namespace RidwanHidayat\BackendTest\Service;

use Monolog\Handler\StreamHandler;
use RidwanHidayat\BackendTest\Controller\TaskController;
use RidwanHidayat\BackendTest\Domain\Task;
use RidwanHidayat\BackendTest\Exception\ValidationException;
use RidwanHidayat\BackendTest\Model\TaskRequest;
use RidwanHidayat\BackendTest\Model\TaskResponse;
use RidwanHidayat\BackendTest\Repository\TaskRepository;
use Firebase\JWT\JWT;
use RidwanHidayat\BackendTest\Helper\Helper;
use Monolog\Logger;

class TaskService
{
    private TaskRepository $taskRepository;
    private Logger $logger;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->logger = new Logger(TaskController::class);
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/application.log'));
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
            $this->logger->info('Invalid arguments');
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
            $this->logger->info('Task not found');
            throw new ValidationException('Task not found');
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