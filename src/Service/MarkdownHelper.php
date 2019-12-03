<?php


namespace App\Service;

use cebe\markdown\MarkdownExtra;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Service\PumlRender;


class MarkdownHelper
{
    private $markdown;
    private $filePathRoot;
    private $allFiles;
    private $umlCliPathFull;
    private $imgStaticPath;
    private $rootDir;
    private $imgExtension = 'svg';
    private $umlRender;

    public function __construct(FilesTree $filesHelper,
                                PumlRender $umlRender,
                                string $p_rootDir,
                                string $p_filePathRoot,
                                string $p_imgStaticPath,
                                string $p_umlCliPath)
    {
        $this->markdown = new MarkdownExtra();
        $this->markdown->html5 = true;
        $this->markdown->keepListStartNumber = true;

        $this->allFiles = $filesHelper->getAllFiles();

        $this->umlRender = $umlRender;
        $this->filePathRoot = $p_rootDir . $p_filePathRoot;
        $this->umlCliPathFull = $p_rootDir . $p_umlCliPath;
        $this->imgStaticPath = $p_imgStaticPath;
        $this->rootDir = $p_rootDir;
    }

    public function parseFile(string $documentFullName)
    {
        if($this->validatePath($documentFullName)){
            $fileData = $this->readFile($documentFullName);
            $documentFullNameParts = pathinfo($documentFullName);
            $parseImage = $this->replaceUml($documentFullNameParts['dirname'], $fileData);
            return $this->markdown->parse($parseImage);
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

    private function replaceUml(string $documentPath, string $data): string {
        $imgPattern = '/(\!\[.*?\]\()(.*\.puml)(\))/';
        return preg_replace_callback($imgPattern, function ($matches )use($documentPath){
            $nameFileUml = $this->umlRender->getFileName("{$this->filePathRoot}{$documentPath}/{$matches[2]}");
            return $matches[1] . $nameFileUml .$matches[3];
        }, $data);
        return $data;
    }

    private function getNameUmlRendered(string $fullPath): string{
        $imgName = $this->getFileName($fullPath);
        if(!file_exists("{$this->rootDir}{$imgName}")){
            $this->convertUmlToSvg($fullPath, $imgName);
        }
        return $imgName;
    }

    private function getFileName(string $fullPath): string {
        $fileBody = $this->getUmlBody($fullPath);
        $hashName = hash("sha256",$fileBody);
        return "{$this->imgStaticPath}/{$hashName}.{$this->imgExtension}";
    }

    private function getUmlBody(string $fullPath): string {
        if(file_exists($fullPath)){
            return file_get_contents($fullPath);
        }
        return '';
    }

    private function convertUmlToSvg(string $fullFileName, string $newFileName) {
        $command = "cat {$this->filePathRoot}{$fullFileName} | java -jar {$this->umlCliPathFull}/plantuml.jar -tsvg -pipe > {$this->rootDir}{$newFileName}";
        exec($command);
    }
}