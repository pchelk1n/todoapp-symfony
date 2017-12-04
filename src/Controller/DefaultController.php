<?php
namespace App\Controller;

use App\Service\TodoService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     *
     * @param TodoService $todoService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(TodoService $todoService)
    {
        $lists = $todoService->getLists();

        return $this->render("default/index.html.twig", ['lists' => $lists]);
    }
}