<?php


namespace App\Service;

use cebe\markdown\MarkdownExtra;
use Symfony\Component\HttpKernel\KernelInterface;


class MarkdownHelper
{
    private $markdown;
    private $filePathRoot;
    private $allFiles;

    public function __construct(KernelInterface $kernel, FilesTree $filesHelper)
    {
        $this->markdown = new MarkdownExtra();
        $this->markdown->html5 = true;
        $this->markdown->keepListStartNumber = true;

        $this->allFiles = $filesHelper->getAllFiles();

        $this->filePathRoot = $kernel->getProjectDir() . $_ENV['FilePathRoot'];
    }

    public function parseFile(string $path)
    {
        if($this->validatePath($path)){
            $fileData = $this->readFile($path);
            return $this->markdown->parse($fileData);
        }
        return 'File not found';
    }

    private function readFile(string $path):string {
        return file_get_contents("{$this->filePathRoot}{$path}");
    }

    private function validatePath(string $path){
        $fullFileName = $this->filePathRoot. $path;
        return in_array($fullFileName, $this->allFiles) && file_exists($fullFileName);
    }
}