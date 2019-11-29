<?php


namespace App\Service;


use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TreeFilesHelper
{
    private $filePathRoot = '';
    private $url;

    public function __construct(KernelInterface $kernel, UrlGeneratorInterface $url)
    {
        $this->filePathRoot = $kernel->getProjectDir() . $_ENV['FilePathRoot'];
        $this->url = $url;
    }

    public function getDirTree():array{
        return $this->readDir($this->filePathRoot, '');
    }

    public function getAllFiles():array{
        return $this->findAllFiles($this->filePathRoot);
    }

    private function excludedDir(string $name):bool {
        $excludedDir = ['.','..'];
        return !in_array($name, $excludedDir);
    }

    private function getDir(string $path):array{
        $files = scandir($path);
        return array_combine($files, array_fill(0, count($files), null));
    }

    private function readDir(string $path, string $url):array{
        $files = $this->getDir($path);
        $result = [];
        foreach ($files as $name => $subName){
            if(!$this->excludedDir($name)){
                continue;
            }
            $currentPath = "{$path}/{$name}";
            $currentUrl = "{$url}/{$name}";
            $buf = ['text'=>$name, ];
            if(is_dir($currentPath) && $this->excludedDir($name)){
                $buf['icon'] = "glyphicon glyphicon-folder-open";
                $buf['nodes'] = $this->readDir($currentPath, $currentUrl);
                $buf['href'] = null;
            }else{
                $buf['icon'] = "glyphicon glyphicon-file";
                $buf['selectedIcon'] = "glyphicon glyphicon-hand-right";
                $buf['href'] = $this->url->generate('file', [
                    'path' => $currentUrl,
                ]);
                $buf['color'] = "blue";
            }
            $result[] = $buf;
        }
        return $result;
    }

    private function findAllFiles(string $path):array{
        $files = $this->getDir($path);
        $result = [];
        foreach ($files as $name => $non){
            if(!$this->excludedDir($name)){
                continue;
            }
            $currentPath = "{$path}/{$name}";
            if(is_dir($currentPath)){
                $result = array_merge($result, $this->findAllFiles($currentPath));
            }else{
                $result[] = $currentPath;
            }
        }
        return $result;
    }
}