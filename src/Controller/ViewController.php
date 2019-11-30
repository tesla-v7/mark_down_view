<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\MarkdownHelper;
use App\Service\FilesTree;


class ViewController extends AbstractController
{
    /**
     * @Route("/", name="view")
     * @param FilesTree $filesHelper
     * @return Response
     */
    public function index(FilesTree $filesHelper): Response
    {
        return $this->render('view/index.html.twig', [
            'filesTree'=> json_encode($filesHelper->getDirTree()),
        ]);
    }

    /**
     * @Route("/file/{path}", name="file", requirements={"path"=".+"})
     * @param MarkdownHelper $markdown
     * @param string $path
     * @return Response
     */
    public function getFile(MarkdownHelper $markdown, string $path): Response
    {
        return new Response($markdown->parseFile('/'.$path));
    }
}
