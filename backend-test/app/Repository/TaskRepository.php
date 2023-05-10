<?php

namespace RidwanHidayat\BackendTest\Repository;

use Monolog\Handler\StreamHandler;
use PDO;
use RidwanHidayat\BackendTest\Controller\TaskController;
use RidwanHidayat\BackendTest\Domain\Task;
use Monolog\Logger;

class TaskRepository
{
    private PDO $connection;
    private Logger $logger;

    public function __construct(PDO $connection)
    {
        $this->logger = new Logger(TaskController::class);
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/application.log'));
        $this->connection = $connection;
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM `tasks`;");
    }

    public function token(string $username, string $password): ?array
    {
        try {
            $statement = $this->connection->prepare("SELECT *
                FROM `auth`
                WHERE `username` = ?
                AND `password` = ?;
            ");

            $statement->execute([$username, $password]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $row;
            } else {
                $this->logger->warning('Unauthorized');
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function save(Task $task): Task
    {
        $statement = $this->connection->prepare("INSERT INTO `tasks`
            (
                `title`,
                `description`,
                `created_at`
            )
            VALUES
            (?, ?, NOW());
        ");

        $statement->execute([
            $task->title,
            $task->description
        ]);

        $task->id = $this->connection->lastInsertId();

        return $task;
    }

    public function findAll(): array
    {
        try {
            $statement = $this->connection->query("SELECT *
            FROM `tasks`;");

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            $data = [];
            foreach ($result as $row) {
                $task = $this->getTask($row);
                $data[] = $task;
            }

            return $data;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findById(int $id): ?Task
    {
        try {
            $statement = $this->connection->prepare("SELECT *
                FROM `tasks`
                WHERE `id` = ?;
            ");

            $statement->execute([$id]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {

                $task = $this->getTask($row);

                return $task;
            } else {
                $this->logger->info('Task not found');
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function update(Task $task): Task
    {
        $statement = $this->connection->prepare("UPDATE `tasks`
            SET
            `title` = ?,
            `description` = ?,
            `is_done` = ?
            WHERE `id` = ?;
            "
        );

        $statement->execute([
            $task->title,
            $task->description,
            $task->isDone,
            $task->id
        ]);

        return $task;
    }

    public function delete(int $id): int
    {
        $statement = $this->connection->prepare("DELETE FROM `tasks`
            WHERE `id` = ?;"
        );

        $statement->execute([$id]);

        return $statement->rowCount();
    }

    /**
     * @param mixed $row
     * @return Task
     */
    protected function getTask(mixed $row): Task
    {
        $task = new Task();
        $task->id = $row['id'];
        $task->title = $row['title'];
        $task->description = $row['description'];
        $task->isDone = $row['is_done'];
        $task->createdAt = $row['created_at'];
        return $task;
    }
}