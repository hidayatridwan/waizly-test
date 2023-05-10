<?php

namespace RidwanHidayat\BackendTest\Controller;

use Monolog\Handler\StreamHandler;
use RidwanHidayat\BackendTest\Config\Database;
use RidwanHidayat\BackendTest\Helper\Helper;
use RidwanHidayat\BackendTest\Model\TaskRequest;
use RidwanHidayat\BackendTest\Repository\TaskRepository;
use RidwanHidayat\BackendTest\Service\TaskService;
use Exception;
use Monolog\Logger;

class TaskController
{
    private TaskService $taskService;
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger(TaskController::class);
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/application.log'));
        $connection = Database::getConnection();
        $taskRepository = new TaskRepository($connection);
        $this->taskService = new TaskService($taskRepository);
    }

    public function token(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        Helper::parseToArray();

        if (!isset($_POST['username']) || !isset($_POST['password'])) {
            $this->logger->info('Invalid arguments');
            $response = [
                'message' => 'Invalid arguments'
            ];
            http_response_code(404);
            echo json_encode($response);
            die;
        }

        $result = $this->taskService->token($_POST['username'], $_POST['password']);

        if ($result != null) {
            $response = [
                'token' => $result
            ];
            http_response_code(200);
        } else {
            $this->logger->warning('Failed to get token');
            $response = [
                'message' => 'Failed to get token'
            ];
            http_response_code(404);
        }

        echo json_encode($response);
    }

    public function save(): void
    {
        Helper::parseToArray();

        try {
            $request = new TaskRequest();
            $request->title = $_POST['title'];
            $request->description = $_POST['description'];
            $result = $this->taskService->save($request);
            $response = [
                'result' => $result
            ];
            http_response_code(201);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            $response = [
                'error' => $exception->getMessage()
            ];
            http_response_code(400);
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
    }

    public function findAll(): void
    {
        $result = $this->taskService->findAll();

        $response = [
            'result' => $result
        ];

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
    }

    public function findById(int $id): void
    {
        $result = $this->taskService->findById($id);

        if ($result != null) {
            $response = [
                'result' => $result
            ];
            http_response_code(200);
        } else {
            $this->logger->info('Task not found');
            $response = [
                'message' => 'Task not found'
            ];
            http_response_code(404);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
    }

    public function update(int $id): void
    {
        Helper::parseToArray();

        try {
            $request = new TaskRequest();
            $request->id = $id;
            $request->title = $_POST['title'];
            $request->description = $_POST['description'];
            $request->isDone = $_POST['isDone'];
            $result = $this->taskService->update($request);
            $response = [
                'result' => $result
            ];
            http_response_code(200);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            $response = [
                'error' => $exception->getMessage()
            ];
            http_response_code(400);
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
    }

    public function delete(): void
    {
        Helper::parseToArray();

        $request = new TaskRequest();
        $request->id = $_POST['id'];
        $result = $this->taskService->delete($request->id);
        if ($result > 0) {
            $response = [
                'result' => 'Successfully deleted'
            ];
            http_response_code(200);
        } else {
            $this->logger->info('Task not found');
            $response = [
                'error' => 'Task not found'
            ];
            http_response_code(400);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
    }
}