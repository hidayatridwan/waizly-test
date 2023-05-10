<?php

namespace RidwanHidayat\BackendTest\Repository;

use PHPUnit\Framework\TestCase;
use RidwanHidayat\BackendTest\Domain\Task;
use RidwanHidayat\BackendTest\Config\Database;

class TaskRepositoryTest extends TestCase
{

    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->taskRepository = new TaskRepository($connection);
        $this->taskRepository->deleteAll();
    }

    public function testSaveSuccess(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $response = $this->taskRepository->save($task);

        self::assertEquals('Membuat kue', $response->title);
    }

    public function testFindAllSuccess(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $this->taskRepository->save($task);

        $response = $this->taskRepository->findAll();

        self::assertCount(1, $response);
    }

    public function testFindByIdSuccess(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $save = $this->taskRepository->save($task);

        $response = $this->taskRepository->findById($save->id);

        self::assertNotNull($response);
    }

    public function testFindByIdNotFound(): void
    {
        $response = $this->taskRepository->findById(0);

        self::assertNull($response);
    }

    public function testUpdateSuccess(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $save = $this->taskRepository->save($task);

        $task = new Task();
        $task->id = $save->id;
        $task->title = 'Mengupdate kue';
        $task->description = 'Mari ikuti saya untuk mengupdate opak dan ranginang';
        $task->isDone = 'Y';
        $response = $this->taskRepository->update($task);

        self::assertEquals('Mengupdate kue', $response->title);
        self::assertEquals('Y', $response->isDone);
    }

    public function testDeleteSuccess(): void
    {
        $task = new Task();
        $task->title = 'Membuat kue';
        $task->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $save = $this->taskRepository->save($task);

        $response = $this->taskRepository->delete($save->id);

        self::assertEquals(1, $response);
    }
}