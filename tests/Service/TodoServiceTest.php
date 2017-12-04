<?php
namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use App\Service\TodoService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class TodoServiceTest extends TestCase
{
    /**
     * @var ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var TaskRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $taskRepository;

    /**
     * @var TodoListRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $todoListRepository;

    /**
     * @var TodoService
     */
    private $todoService;

    protected function setUp()
    {
        $this->em = $this->createMock(ObjectManager::class);
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->todoListRepository = $this->createMock(TodoListRepository::class);

        $this->todoService = new TodoService($this->em, $this->taskRepository, $this->todoListRepository);
    }

    public function testGetLists()
    {
        $this->todoListRepository->expects($this->once())
            ->method('getAvailableLists')
            ->willReturn([new TodoList(), new TodoList()]);

        $list = $this->todoService->getLists();
        $this->assertCount(2, $list);
    }

    public function testGetList()
    {
        $listId = 1;
        $list = new TodoList();

        $this->todoListRepository->expects($this->once())
            ->method('getList')
            ->with($listId)
            ->willReturn($list);

        $result = $this->todoService->getList($listId);
        $this->assertNotEmpty($result);
    }

    public function testCreateList()
    {
        $this->em->expects($this->once())->method('flush');

        $todoList = $this->todoService->createList("test");
        $this->assertEquals("test", $todoList['name']);
    }

    public function testGetTasks()
    {
        $listId = 1;
        $this->taskRepository->expects($this->once())
            ->method('getListTasks')
            ->with($listId)
            ->willReturn([$this->getTask()]);

        $list = $this->todoService->getTasks($listId);
        $this->assertCount(1, $list);
    }

    public function testGetTask()
    {
        $taskId = 1;
        $listId = 2;
        $task = $this->getTask();

        $this->taskRepository->expects($this->once())
            ->method('getTask')
            ->with($taskId, $listId)
            ->willReturn($task);

        $result = $this->todoService->getTask($listId, $taskId);
        $this->assertEquals("test", $result['text']);
    }


    public function testAddTask()
    {
        $listId = 1;
        $list = new TodoList();

        $this->todoListRepository->expects($this->once())
            ->method('getList')
            ->with($listId)
            ->willReturn($list);

        $this->em->expects($this->atLeastOnce())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $task = $this->todoService->addTask($listId, "test");
        $this->assertEquals("test", $task['text']);
    }

    public function testUpdateTask()
    {
        $listId = 1;
        $taskId = 1;
        $task = $this->getTask();

        $this->taskRepository->expects($this->once())
            ->method('getTask')
            ->with($taskId, $listId)
            ->willReturn($task);

        $this->em->expects($this->once())->method('persist')->with($task);
        $this->em->expects($this->once())->method('flush');
        $task = $this->todoService->updateTask($listId, $taskId, "test updated");
        $this->assertEquals("test updated", $task['text']);
    }

    public function testRemoveTask()
    {
        $listId = 1;
        $taskId = 1;
        $task = $this->getTask();

        $this->taskRepository->expects($this->once())
            ->method('getTask')
            ->with($listId, $taskId)
            ->willReturn($task);

        $this->em->expects($this->once())->method('remove')->with($task);
        $this->em->expects($this->once())->method('flush');

        $this->todoService->removeTask($listId, $taskId);
    }

    public function testResolveTask()
    {
        $listId = 1;
        $taskId = 1;
        $task = $this->getTask();

        $this->taskRepository->expects($this->once())
            ->method('getTask')
            ->with($listId, $taskId)
            ->willReturn($task);

        $this->em->expects($this->once())->method('flush');
        $task = $this->todoService->resolveTask($listId, $taskId);
        $this->assertEquals('Done', $task['status']);
    }

    private function getTask()
    {
        $list = new TodoList();
        $list->setId(1);
        $list->setName("test list");

        $task = new Task();
        $task->setId(2);
        $task->setText("test");

        $list->addTask($task);
        $task->setTodoList($list);

        return $task;
    }
}
