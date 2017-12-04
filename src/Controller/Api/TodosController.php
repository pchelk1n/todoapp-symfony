<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Service\TodoService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/todos", defaults={"_format":"json"})
 */
class TodosController extends AbstractController
{
    /**
     * @Route("/")
     * @Method("GET")
     *
     * @param TodoService $todoService
     * @return JsonResponse
     */
    public function index(TodoService $todoService)
    {
        $lists = $todoService->getLists();

        return $this->json($lists);
    }

    /**
     * @Route("/{id}")
     * @Method("GET")
     *
     * @param Request $request
     * @param TodoService $todoService
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getList(Request $request, TodoService $todoService)
    {
        $listId = $request->attributes->getInt('id');

        $list = $todoService->getList($listId);

        return $this->json($list);
    }

    /**
     * @Route("/")
     * @Method("POST")
     *
     * @param Request $request
     * @param TodoService $todoService
     * @return JsonResponse
     */
    public function createList(Request $request, TodoService $todoService)
    {
        $text = $request->request->get('text');

        $list = $todoService->createList($text);

        return $this->json($list);
    }

    /**
     * @Route("/{taskListId}/")
     * @Method("GET")
     *
     * @param Request $request
     * @param TodoService $todoService
     * @return JsonResponse
     */
    public function getTasks(Request $request, TodoService $todoService)
    {
        $listId = $request->attributes->getInt('taskListId');

        $tasks = $todoService->getTasks($listId);

        return $this->json($tasks);
    }


    /**
     * @Route("/{taskListId}/{id}")
     * @Method("GET")
     *
     * @param Request $request
     * @param TodoService $todoService
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTask(Request $request, TodoService $todoService)
    {
        $listId = $request->attributes->getInt('taskListId');
        $id = $request->attributes->getInt('id');

        $task = $todoService->getTask($listId, $id);

        return $this->json($task);
    }


    /**
     * @Route("/{taskListId}/")
     * @Method("POST")
     *
     * @param Request $request
     * @param TodoService $todoService
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function addTask(Request $request, TodoService $todoService)
    {
        $listId = $request->attributes->getInt('taskListId');
        $text = $request->request->get('text');

        $task = $todoService->addTask($listId, $text);

        return $this->json($task);
    }

    /**
     * @Route("/{taskListId}/{taskId}")
     * @Method("POST")
     *
     * @param Request $request
     * @param TodoService $todoService
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function editTask(Request $request, TodoService $todoService)
    {
        $listId = $request->attributes->getInt('taskListId');
        $taskId = $request->attributes->getInt('taskId');
        $text = $request->request->get('text');

        $task = $todoService->updateTask($listId, $taskId, $text);

        return $this->json($task);
    }

    /**
     * @Route("/{taskListId}/{taskId}")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param TodoService $todoService
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removeTask(Request $request, TodoService $todoService)
    {
        $listId = $request->attributes->getInt('taskListId');
        $taskId = $request->attributes->getInt('taskId');

        $todoService->removeTask($listId, $taskId);

        return $this->json([]);
    }

    /**
     * @Route("/{taskListId}/{taskId}/done")
     * @Method("POST")
     *
     * @param Request $request
     * @param TodoService $todoService
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function markTaskDone(Request $request, TodoService $todoService)
    {
        $listId = $request->attributes->getInt('taskListId');
        $taskId = $request->attributes->getInt('taskId');

        $task = $todoService->resolveTask($listId, $taskId);

        return $this->json($task);
    }
}
