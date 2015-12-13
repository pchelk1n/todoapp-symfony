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
     * @return array
     */
    public function getLists()
    {
        $lists = $this->todoListRepository->getAvailableLists();

        return array_map(
            function (TodoList $list) {
                return $this->formatList($list);
            },
            $lists
        );
    }

    /**
     * @param int $listId
     * @return array
     */
    public function getList($listId)
    {
        $list = $this->todoListRepository->getList($listId);

        return $this->formatList($list);
    }

    /**
     * @param string $text
     * @return array
     */
    public function createList($text)
    {
        $todoList = new TodoList();
        $todoList->setName($text);

        $this->objectManager->persist($todoList);
        $this->objectManager->flush();

        return $this->formatList($todoList);
    }

    /**
     * @param int $listId
     * @return array
     */
    public function getTasks($listId)
    {
        $tasks = $this->taskRepository->getListTasks($listId);

        return array_map(
            function (Task $task) {
                return $this->formatTask($task);
            },
            $tasks
        );
    }

    /**
     * @param int $listId
     * @param int $taskId
     * @return array
     */
    public function getTask($listId, $taskId)
    {
        $task = $this->taskRepository->getTask($taskId, $listId);

        return $this->formatTask($task);
    }

    /**
     * @param int $listId
     * @param string $text
     * @return array
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

        return $this->formatTask($task);
    }

    /**
     * @param int $listId
     * @param int $taskId
     * @param string $text
     * @return array
     */
    public function updateTask($listId, $taskId, $text)
    {
        $task = $this->taskRepository->getTask($taskId, $listId);
        $task->setText($text);

        $this->objectManager->persist($task);
        $this->objectManager->flush();

        return $this->formatTask($task);
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
     * @return array
     */
    public function resolveTask($listId, $taskId)
    {
        $task = $this->taskRepository->getTask($listId, $taskId);
        $task->setStatus(Task::STATUS_DONE);

        $this->objectManager->persist($task);
        $this->objectManager->flush();

        return $this->formatTask($task);
    }

    /**
     * @param TodoList $list
     * @return array
     */
    private function formatList(TodoList $list)
    {
        return [
            'id' => $list->getId(),
            'name' => $list->getName(),
            'tasks' => $list->getTasks()->map(
                function (Task $task) {
                    return $task->getId();
                }
            )->toArray(),
            'total_tasks' => $list->getTasks()->count()
        ];
    }

    /**
     * @param Task $task
     * @return array
     */
    private function formatTask(Task $task)
    {
        return [
            'id' => $task->getId(),
            'text' => $task->getText(),
            'status' => $task->getStatus() === Task::STATUS_DONE ? 'Done' : 'Undone',
            'created_at' => $task->getCreatedAt(),
            'list' => $task->getTodoList()->getId()
        ];
    }
}