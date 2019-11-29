<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\MarkdownHelper;
use App\Service\TreeFilesHelper;


class ViewController extends AbstractController
{
    /**
     * @Route("/", name="view")
     */
    public function index(TreeFilesHelper $filesHelper)
    {
        return $this->render('view/index.html.twig', [
            'controller_name' => 'ViewController',
            'filesTree'=> json_encode($filesHelper->getDirTree()),
        ]);
    }

    /**
     * @Route("/file/", name="file")
     */
    public function getFile(MarkdownHelper $markdown)
    {
        $response = new Response(
            '',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        $request = Request::createFromGlobals();
        $path = $request->query->get('path');

        $response->setContent($markdown->parseFile($path));
        return $response;
    }
}
