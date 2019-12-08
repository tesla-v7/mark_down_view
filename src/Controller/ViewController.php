<?php

namespace App\Controller;

use App\Service\PathRepo;
use App\Service\PumlRender;
use PhpUnitCoverageTest\BarCov;
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
     * @Route("/static/img/{path}", name="static", requirements={"path"=".+"})
     * @param string $path
     * @return Response
     */
    public function readFile(string $path, PathRepo $pathRepo):Response{
        $fullFilePath = $pathRepo->getImgStaticPath() ."/{$path}";
        if(file_exists($fullFilePath)){
            $fileContent = file_get_contents($fullFilePath);
            return new Response($fileContent,
                Response::HTTP_OK,
                ['content-type' => mime_content_type($fullFilePath)]
            );
        }

        $fullErrorFilePath = $pathRepo->getImgStaticPath() ."/not_found.jpg";
        $fileErrorContent = file_get_contents($fullErrorFilePath);
        return new Response($fileErrorContent,
            Response::HTTP_OK,
            ['content-type' => mime_content_type($fullErrorFilePath)]
        );
    }

    /**
     * @Route("/git/pull", name="git")
     * @param string $p_rootDir
     * @param string $p_gitFilePathRoot
     * @return Response
     */
    public function gitPull(string $p_rootDir, string $p_gitFilePathRoot):Response{
        $fullFileDir = "{$p_rootDir}{$p_gitFilePathRoot}";
        $process = new Process(['git', 'pull'], $fullFileDir);
        $process->run();
        return new Response('');
    }

}
