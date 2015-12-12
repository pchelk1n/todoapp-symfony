<?php
namespace AppBundle\Tests\Service;

use AppBundle\Entity\Task;
use AppBundle\Entity\TodoList;
use AppBundle\Repository\TaskRepository;
use AppBundle\Repository\TodoListRepository;
use AppBundle\Service\TodoService;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit_Framework_MockObject_MockObject;

class TodoServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $em;

    /**
     * @var TaskRepository|PHPUnit_Framework_MockObject_MockObject
     */
    protected $taskRepository;

    /**
     * @var TodoListRepository|PHPUnit_Framework_MockObject_MockObject
     */
    protected $todoListRepository;

    /**
     * @var TodoService
     */
    protected $todoService;

    protected function setUp()
    {
        $this->em = $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->taskRepository = $this->getMockBuilder(TaskRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->todoListRepository = $this->getMockBuilder(TodoListRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->todoService = new TodoService($this->em, $this->taskRepository, $this->todoListRepository);
    }

    public function testGetLists()
    {
        $this->todoListRepository->expects($this->once())
            ->method('getAvailableLists')
            ->willReturn([1, 2]);

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
        $this->assertEquals($list, $result);
    }

    public function testCreateList()
    {
        $this->em->expects($this->once())->method('flush');

        $todoList = $this->todoService->createList("test");
        $this->assertEquals("test", $todoList->getName());
    }

    public function testGetTasks()
    {
        $listId = 1;
        $this->taskRepository->expects($this->once())
            ->method('getListTasks')
            ->with($listId)
            ->willReturn([new Task()]);

        $list = $this->todoService->getTasks($listId);
        $this->assertCount(1, $list);
    }

    public function testGetTask()
    {
        $taskId = 1;
        $listId = 2;
        $task = new Task();

        $this->taskRepository->expects($this->once())
            ->method('getTask')
            ->with($taskId, $listId)
            ->willReturn($task);

        $result = $this->todoService->getTask($listId, $taskId);
        $this->assertEquals($task, $result);
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
        $this->assertEquals("test", $task->getText());
    }

    public function testUpdateTask()
    {
        $listId = 1;
        $taskId = 1;
        $task = new Task();

        $this->taskRepository->expects($this->once())
            ->method('getTask')
            ->with($taskId, $listId)
            ->willReturn($task);

        $this->em->expects($this->once())->method('persist')->with($task);
        $this->em->expects($this->once())->method('flush');
        $task = $this->todoService->updateTask($listId, $taskId, "test updated");
        $this->assertEquals("test updated", $task->getText());
    }

    public function testRemoveTask()
    {
        $listId = 1;
        $taskId = 1;
        $task = new Task();

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
        $task = new Task();

        $this->taskRepository->expects($this->once())
            ->method('getTask')
            ->with($listId, $taskId)
            ->willReturn($task);

        $this->em->expects($this->once())->method('flush');
        $task = $this->todoService->resolveTask($listId, $taskId);
        $this->assertEquals(Task::STATUS_DONE, $task->getStatus());
    }
}