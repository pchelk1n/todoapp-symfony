<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Task;
use AppBundle\Entity\TodoList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/todos", defaults={"_format":"json"})
 */
class TodosController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     * @return JsonResponse
     */
    public function index()
    {
        $lists = $this->getTodoService()->getLists();

        $data = array_map(
            function (TodoList $list) {
                return $this->formatList($list);
            },
            $lists
        );

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $listId = $request->attributes->getInt('id');

        $list = $this->getTodoService()->getList($listId);

        return new JsonResponse($this->formatList($list));
    }

    /**
     * @Route("/")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function createList(Request $request)
    {
        $text = $request->request->get('text');

        $list = $this->getTodoService()->createList($text);

        return new JsonResponse($this->formatList($list));
    }

    /**
     * @Route("/{taskListId}/")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function getTasks(Request $request)
    {
        $listId = $request->attributes->getInt('taskListId');

        $tasks = $this->getTodoService()->getTasks($listId);

        $data = array_map(
            function (Task $task) {
                return $this->formatTask($task);
            },
            $tasks
        );

        return new JsonResponse($data);
    }


    /**
     * @Route("/{taskListId}/{id}")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function getTask(Request $request)
    {
        $listId = $request->attributes->getInt('taskListId');
        $id = $request->attributes->getInt('id');

        $task = $this->getTodoService()->getTask($listId, $id);

        return new JsonResponse($this->formatTask($task));
    }


    /**
     * @Route("/{taskListId}/")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function addTask(Request $request)
    {
        $listId = $request->attributes->getInt('taskListId');
        $text = $request->request->get('text');

        $task = $this->getTodoService()->addTask($listId, $text);

        return new JsonResponse($this->formatTask($task));
    }

    /**
     * @Route("/{taskListId}/{taskId}")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function editTask(Request $request)
    {
        $listId = $request->attributes->getInt('taskListId');
        $taskId = $request->attributes->getInt('taskId');
        $text = $request->request->get('text');

        $task = $this->getTodoService()->updateTask($listId, $taskId, $text);

        return new JsonResponse($this->formatTask($task));
    }

    /**
     * @Route("/{taskListId}/{taskId}")
     * @Method("DELETE")
     * @param Request $request
     * @return JsonResponse
     */
    public function removeTask(Request $request)
    {
        $listId = $request->attributes->getInt('taskListId');
        $taskId = $request->attributes->getInt('taskId');

        $this->getTodoService()->removeTask($listId, $taskId);

        return new JsonResponse();
    }

    /**
     * @Route("/{taskListId}/{taskId}/done")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function markTaskDone(Request $request)
    {
        $listId = $request->attributes->getInt('taskListId');
        $taskId = $request->attributes->getInt('taskId');

        $task = $this->getTodoService()->resolveTask($listId, $taskId);

        return new JsonResponse($this->formatTask($task));
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
            )->toArray()
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

    /**
     * @return \AppBundle\Service\TodoService
     */
    private function getTodoService()
    {
        return $this->get('app.service.todo');
    }
}
