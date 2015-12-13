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

        return new JsonResponse($lists);
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

        return new JsonResponse($list);
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

        return new JsonResponse($list);
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

        return new JsonResponse($tasks);
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

        return new JsonResponse($task);
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

        return new JsonResponse($task);
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

        return new JsonResponse($task);
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

        return new JsonResponse($task);
    }

    /**
     * @return \AppBundle\Service\TodoService
     */
    private function getTodoService()
    {
        return $this->get('app.service.todo');
    }
}
