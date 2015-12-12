<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function index()
    {
        $lists = $this->get('app.service.todo')->getLists();

        return $this->render("default/index.html.twig", ['lists' => $lists]);
    }
}