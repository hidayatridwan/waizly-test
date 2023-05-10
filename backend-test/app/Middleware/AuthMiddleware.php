<?php

namespace RidwanHidayat\BackendTest\Middleware;

use RidwanHidayat\BackendTest\Config\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RidwanHidayat\BackendTest\Helper\Helper;
use RidwanHidayat\BackendTest\Repository\TaskRepository;
use Exception;

class AuthMiddleware implements Middleware
{

    private TaskRepository $taskRepository;

    public function __construct()
    {
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
                    header('Content-Type: application/json; charset=utf-8');
                    http_response_code(401);
                    echo json_encode(['message' => 'Unauthorized']);
                    exit;
                }
            } catch (Exception $exception) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(401);
                echo json_encode(['message' => 'Unauthorized']);
                exit;
            }
        } else {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            exit;
        }
    }
}