<?php

namespace RidwanHidayat\BackendTest\Controller;

function header(): void
{
    echo 'Content-Type: application/json; charset=utf-8';
}

use PHPUnit\Framework\TestCase;
use RidwanHidayat\BackendTest\Config\Database;
use RidwanHidayat\BackendTest\Domain\Task;
use RidwanHidayat\BackendTest\Repository\TaskRepository;

class TaskControllerTest extends TestCase
{

    private TaskRepository $taskRepository;
    private TaskController $taskController;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->taskRepository = new TaskRepository($connection);
        $this->taskController = new TaskController();

        $this->taskRepository->deleteAll();
    }

    public function testSaveSuccess(): void
    {
        $_POST['title'] = 'Membuat kue';
        $_POST['description'] = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $this->taskController->save();

        self::assertEquals(201, http_response_code());
    }

    public function testSaveFailed(): void
    {
        $_POST['title'] = '';
        $_POST['description'] = '';
        $this->taskController->save();

        self::assertEquals(400, http_response_code());
    }

    public function testFindAllSuccess(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $this->taskRepository->save($task);

        $this->taskController->findAll();

        self::assertEquals(200, http_response_code());
        $this->expectOutputRegex('[result]');
    }

    public function testFindByIdSuccess(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $save = $this->taskRepository->save($task);

        $this->taskController->findById($save->id);

        self::assertEquals(200, http_response_code());
        $this->expectOutputRegex('[result]');
    }

    public function testFindByIdNotFound(): void
    {
        $this->taskController->findById(0);

        self::assertEquals(404, http_response_code());
        $this->expectOutputRegex('[Task not found]');
    }

    public function testUpdateSuccess(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $save = $this->taskRepository->save($task);

        $_POST['title'] = 'Mengupdate kue';
        $_POST['description'] = 'Mari ikuti saya untuk mengupdate opak dan ranginang';
        $_POST['isDone'] = 'Y';
        $this->taskController->update($save->id);

        self::assertEquals(200, http_response_code());
        $this->expectOutputRegex('[result]');
    }

    public function testUpdateNotFound(): void
    {
        $this->taskController->update(0);

        self::assertEquals(400, http_response_code());
        $this->expectOutputRegex('[Task not found]');
    }

    public function testUpdateFailed(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $save = $this->taskRepository->save($task);

        $_POST['title'] = '';
        $_POST['description'] = '';
        $_POST['isDone'] = '';
        $this->taskController->update($save->id);

        self::assertEquals(400, http_response_code());
        $this->expectOutputRegex('[error]');
    }

    public function testDeleteSuccess(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $save = $this->taskRepository->save($task);

        $_POST['id'] = $save->id;
        $this->taskController->delete();

        self::assertEquals(200, http_response_code());
    }

    public function testDeleteNotFound(): void
    {
        $_POST['id'] = 0;
        $this->taskController->delete();

        self::assertEquals(400, http_response_code());
    }
}