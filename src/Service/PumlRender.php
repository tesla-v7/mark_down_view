<?php


namespace App\Service;


class PumlRender
{
    private $rootDir;
    private $imgStaticPath;
    private $renderPumlPath;
    private $umlCliPath;

    private $pumlExtension = '.svg';

    private $fileNotFound = 'file_not_found';


    public function __construct(string $p_rootDir,
                                string $p_imgStaticPath,
                                string $p_renderPumlPath,
                                string $p_umlCliPath)
    {
        $this->rootDir = $p_rootDir;
        $this->umlCliPath = "{$p_rootDir}{$p_umlCliPath}/plantuml.jar";
        $this->renderPumlPath = $p_renderPumlPath;
        $this->imgStaticPath = $p_imgStaticPath;
    }

    public function getFileName(string $documentPath):string {
        $realPath = realpath($documentPath);
        if($realPath === false){
            return '/static/img/not_found.jpg';
        }

        $pumlHashFileName = $this->getHashName($realPath);
        $pumlStaticFullName = "{$this->renderPumlPath}/{$pumlHashFileName}{$this->pumlExtension}";
        $this->convertUmlToSvg($realPath, $pumlStaticFullName);

        return $pumlStaticFullName;
    }

    private function convertUmlToSvg(string $fullFileName, string $pumlStaticFullName) {
        if(file_exists("{$this->rootDir}{$pumlStaticFullName}")){
            return;
        }
        $command = "cat {$fullFileName} | java -jar {$this->umlCliPath} -tsvg -pipe > {$this->rootDir}{$pumlStaticFullName}";
        exec($command);
    }

    private function getHashName(string $pumlFullName):string {
        $pumlFileBody = $this->readFile($pumlFullName);
        if($pumlFileBody === $this->fileNotFound){
            return $this->fileNotFound;
        }
        return hash("sha256",$pumlFileBody);
    }

    private function readFile(string $pumlFullName):string {
        if(!file_exists($pumlFullName)){
            return $this->fileNotFound;
        }
        return file_get_contents("{$pumlFullName}");
    }

}