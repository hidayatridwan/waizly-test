<?php

namespace RidwanHidayat\BackendTest\Service;

use PHPUnit\Framework\TestCase;
use RidwanHidayat\BackendTest\Config\Database;
use RidwanHidayat\BackendTest\Exception\ValidationException;
use RidwanHidayat\BackendTest\Model\TaskRequest;
use RidwanHidayat\BackendTest\Repository\TaskRepository;

class TaskServiceTest extends TestCase
{

    private TaskRepository $taskRepository;
    private TaskService $taskService;


    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->taskRepository = new TaskRepository($connection);
        $this->taskService = new TaskService($this->taskRepository);

        $this->taskRepository->deleteAll();
    }

    public function testSaveSuccess(): void
    {
        $request = new TaskRequest();
        $request->title = 'Membuat kue';
        $request->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $response = $this->taskService->save($request);

        self::assertEquals('Membuat kue', $response->task->title);
    }

    public function testSaveFailed(): void
    {
        $this->expectException(ValidationException::class);

        $request = new TaskRequest();
        $request->title = '';
        $request->description = '';
        $this->taskService->save($request);
    }

    public function testFindAllSuccess(): void
    {
        $request = new TaskRequest();
        $request->title = 'Membuat kue';
        $request->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $this->taskService->save($request);

        $response = $this->taskService->findAll();

        self::assertCount(1, $response);
    }

    public function testFindByIdSuccess()
    {
        $request = new TaskRequest();
        $request->title = 'Membuat kue';
        $request->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $save = $this->taskService->save($request);

        $response = $this->taskService->findById($save->task->id);

        self::assertEquals($request->title, $response->title);
    }

    public function testFindByIdNotFound()
    {
        $response = $this->taskService->findById(0);

        self::assertNull($response);
    }

    public function testUpdateSuccess(): void
    {
        $request = new TaskRequest();
        $request->title = 'Membuat kue';
        $request->description = 'Mari ikuti saya untuk membuat opak dan ranginang';
        $save = $this->taskService->save($request);

        $request = new TaskRequest();
        $request->id = $save->task->id;
        $request->title = 'Mengupdate kue';
        $request->description = 'Mari ikuti saya untuk mengupdate opak dan ranginang';
        $request->isDone = 'Y';
        $response = $this->taskService->update($request);

        self::assertEquals($request->title, $response->task->title);
    }

    public function testUpdateFailed(): void
    {
        $this->expectException(ValidationException::class);

        $request = new TaskRequest();
        $request->title = '';
        $request->description = '';
        $this->taskService->update($request);
    }
}