<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\MarkdownHelper;
use App\Service\FilesTree;
use Symfony\Component\Process\Process;


/**
 * Class ViewController
 * @package App\Controller
 */
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

    /**
     * @Route("/static/{path}", name="static", requirements={"path"=".+"})
     * @param string $path
     * @return Response
     */
    public function readFile(string $path, string $p_rootDir, string $p_filePathRoot):Response{
        $fullFilePath = $p_rootDir . '/static' ."/{$path}";
        $fileContent = 'file not found';
        if(file_exists($fullFilePath)){
            $fileContent = file_get_contents($fullFilePath);
        }
        return new Response($fileContent,
            Response::HTTP_OK,
            ['content-type' => 'image/svg+xml']
        );
    }

    /**
     * @Route("/git/pull", name="git")
     * @param string $p_rootDir
     * @param string $p_renderPumlPath
     * @return Response
     */
    public function gitPull(string $p_rootDir, string $p_renderPumlPath):Response{
        $fullFileDir = "{$p_rootDir}{$$p_renderPumlPath}";
        $process = new Process(['git', 'pull'], $fullFileDir);
        $process->run();
        return new Response('');
    }

}
