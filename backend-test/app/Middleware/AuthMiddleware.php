<?php

namespace RidwanHidayat\BackendTest\Middleware;

use Monolog\Handler\StreamHandler;
use RidwanHidayat\BackendTest\Config\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RidwanHidayat\BackendTest\Controller\TaskController;
use RidwanHidayat\BackendTest\Helper\Helper;
use RidwanHidayat\BackendTest\Repository\TaskRepository;
use Exception;
use Monolog\Logger;

class AuthMiddleware implements Middleware
{

    private TaskRepository $taskRepository;
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger(TaskController::class);
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/application.log'));
        $this->taskRepository = new TaskRepository(Database::getConnection());
    }

    function before(): void
    {
        $key = Helper::$key;
        $token = getallheaders()['token'] ?? null;

        if ($token != null) {
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                $result = $this->taskRepository->token($decoded->username, $decoded->password);

                if ($result == null) {
                    $this->logger->warning('User not found');
                    header('Content-Type: application/json; charset=utf-8');
                    http_response_code(401);
                    echo json_encode(['message' => 'Unauthorized']);
                    exit;
                }
            } catch (Exception $exception) {
                $this->logger->warning('Unauthorized');
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(401);
                echo json_encode(['message' => 'Unauthorized']);
                exit;
            }
        } else {
            $this->logger->warning('Token not found');
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            exit;
        }
    }
}