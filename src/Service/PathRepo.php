<?php


namespace App\Service;


class PathRepo {

    private $rootDir;
    private $fileDocPath;
    private $fileGitPath;
    private $umlCliPath;
    private $imgStaticPath;
    private $renderPumlPath;

    public function __construct(string $p_rootDir,
                                string $p_filePathRoot,
                                string $p_gitFilePathRoot,
                                string $p_umlCliPath,
                                string $p_imgStaticPath,
                                string $p_renderPumlPath) {
        $this->rootDir =$p_rootDir;
        $this->fileDocPath = $p_filePathRoot;
        $this->umlCliPath = $p_umlCliPath;
        $this->imgStaticPath = $p_imgStaticPath;
        $this->renderPumlPath = $p_renderPumlPath;
        $this->fileGitPath = $p_gitFilePathRoot;
    }

    public function getRootPath(bool $absolutePat = true):string {
        return $absolutePat ? $this->rootDir : './';
    }

    public function getFileDocPath(bool $absolutePat = true):string {
        return $absolutePat ? "{$this->rootDir}{$this->fileDocPath}" : $this->fileGitPath;
    }

    public function getRenderCliPath(bool $absolutePat = true):string {
        return $absolutePat ? "{$this->rootDir}{$this->umlCliPath}": $this->umlCliPath;
    }

    public function getImgStaticPath(bool $absolutePat = true):string {
        return $absolutePat ? "{$this->rootDir}{$this->imgStaticPath}" : $this->imgStaticPath;
    }

    public function getFileGitPath(bool $absolutePat = true):string {
        return $absolutePat ? "{$this->rootDir}{$this->fileGitPath}" : $this->fileGitPath;
    }

    public function getPathOfRenderedOfPuml(bool $absolutePath = true):string {
        return $absolutePath ? "{$this->rootDir}{$this->renderPumlPath}" : $this->renderPumlPath;
    }
}