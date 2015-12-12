<?php
namespace AppBundle\Service;

use AppBundle\Entity\Task;
use AppBundle\Entity\TodoList;
use AppBundle\Repository\TaskRepository;
use AppBundle\Repository\TodoListRepository;
use Doctrine\Common\Persistence\ObjectManager;

class TodoService
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var TodoListRepository
     */
    private $todoListRepository;

    /**
     * TodoService constructor.
     * @param ObjectManager $objectManager
     * @param TaskRepository $taskRepository
     * @param TodoListRepository $todoListRepository
     */
    public function __construct(
        ObjectManager $objectManager,
        TaskRepository $taskRepository,
        TodoListRepository $todoListRepository
    ) {
        $this->objectManager = $objectManager;
        $this->taskRepository = $taskRepository;
        $this->todoListRepository = $todoListRepository;
    }

    /**
     * @return TodoList[]
     */
    public function getLists()
    {
        return $this->todoListRepository->getAvailableLists();
    }

    /**
     * @param int $listId
     * @return TodoList
     */
    public function getList($listId)
    {
        return $this->todoListRepository->getList($listId);
    }

    /**
     * @param string $text
     * @return TodoList
     */
    public function createList($text)
    {
        $todoList = new TodoList();
        $todoList->setName($text);

        $this->objectManager->persist($todoList);
        $this->objectManager->flush();

        return $todoList;
    }

    /**
     * @param int $listId
     * @return Task[]
     */
    public function getTasks($listId)
    {
        return $this->taskRepository->getListTasks($listId);
    }

    /**
     * @param int $listId
     * @param int $taskId
     * @return Task
     */
    public function getTask($listId, $taskId)
    {
        return $this->taskRepository->getTask($taskId, $listId);
    }

    /**
     * @param int $listId
     * @param string $text
     * @return Task
     */
    public function addTask($listId, $text)
    {
        $task = new Task();
        $task->setText($text);

        $todoList = $this->todoListRepository->getList($listId);
        $todoList->addTask($task);

        $task->setTodoList($todoList);

        $this->objectManager->persist($task);
        $this->objectManager->persist($todoList);
        $this->objectManager->flush();

        return $task;
    }

    /**
     * @param int $listId
     * @param int $taskId
     * @param string $text
     * @return Task
     */
    public function updateTask($listId, $taskId, $text)
    {
        $task = $this->taskRepository->getTask($taskId, $listId);
        $task->setText($text);

        $this->objectManager->persist($task);
        $this->objectManager->flush();

        return $task;
    }

    /**
     * @param int $listId
     * @param int $taskId
     */
    public function removeTask($listId, $taskId)
    {
        $task = $this->taskRepository->getTask($listId, $taskId);

        $this->objectManager->remove($task);
        $this->objectManager->flush();
    }

    /**
     * @param int $listId
     * @param int $taskId
     * @return Task
     */
    public function resolveTask($listId, $taskId)
    {
        $task = $this->taskRepository->getTask($listId, $taskId);
        $task->setStatus(Task::STATUS_DONE);

        $this->objectManager->persist($task);
        $this->objectManager->flush();

        return $task;
    }
}