<?php


namespace App\Service;


class PumlRender {
    private $rootPath;
    private $umlCliFullPath;

    private $pathRepo;

    const PUML_EXTENSION = '.svg';

    const FILE_NOT_FOUNT_PATH = '/static/img/not_found.jpg';


    public function __construct(PathRepo $pathRepo) {
        $this->umlCliFullPath = "{$pathRepo->getRenderCliPath()}/plantuml.jar";
        $this->pathRepo = $pathRepo;
        $this->rootPath = $pathRepo->getRootPath();
    }

    public function getFileName(string $documentPath): string {
        $realPath = realpath($documentPath);
        if ($realPath === false) {
            return $this::FILE_NOT_FOUNT_PATH;
        }
        try {
            $pumlHashFileName = $this->getHashName($realPath);
        } catch (\Exception $e) {
            return $this::FILE_NOT_FOUNT_PATH;
        }
        $pumlStaticFullName = $this->getFullName($pumlHashFileName);
        $this->convertUmlToSvg($realPath, $pumlStaticFullName);

        return $pumlStaticFullName;
    }

    private function convertUmlToSvg(string $fullFileName, string $pumlStaticFullName) {
        $pumlRenderedPath = "{$this->rootPath}{$pumlStaticFullName}";
        if (file_exists($pumlRenderedPath)) {
            return;
        }
        $command = "cat {$fullFileName} | java -jar {$this->umlCliFullPath} -charset UTF-8 -tsvg -pipe > {$pumlRenderedPath}";
        exec($command);
    }

    private function getHashName(string $pumlFullName): string {
        $pumlFileBody = $this->readFile($pumlFullName);
        return hash("sha256", $pumlFileBody);
    }

    private function readFile(string $pumlFullName): string {
        if (!file_exists($pumlFullName)) {
            throw new \Exception($this::FILE_NOT_FOUNT_PATH);
        }
        return file_get_contents("{$pumlFullName}");
    }

    private function getFullName(string $hashName): string {
        return "{$this->pathRepo->getPathOfRenderedOfPuml(false)}/{$hashName}" . $this::PUML_EXTENSION;
    }

}