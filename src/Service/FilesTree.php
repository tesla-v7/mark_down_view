<?php


namespace App\Service;


use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FilesTree
{
    private $filePathRoot;
    private $urlGenerator;

    public function __construct(string $p_rootDir,
                                string $p_filePathRoot,
                                UrlGeneratorInterface $urlGenerator)
    {
        $this->filePathRoot = $p_rootDir . $p_filePathRoot;
        $this->urlGenerator = $urlGenerator;
    }

    public function getDirTree(): array
    {
        return $this->readDir($this->filePathRoot, '');
    }

    public function getAllFiles(): array
    {
        return $this->findAllFiles($this->filePathRoot);
    }

    private function excludedDir(string $name): bool
    {
        $excludedDir = ['.', '..'];
        return !in_array($name, $excludedDir);
    }

    private function onlyMdFile(string $name): bool
    {
        return (bool)preg_match('/.*\.md$/', $name);
    }

    private function getDir(string $path): array
    {
        $files = scandir($path);
        return array_combine($files, array_fill(0, count($files), null));
    }

    private function readDir(string $path, string $url): array
    {
        $files = $this->getDir($path);
        $result = [];
        foreach ($files as $name => $subName) {
            $currentPath = "{$path}/{$name}";
            if ($this->filterName($currentPath, $name)) {
                continue;
            }
            $currentUrl = $url === '' ? $name : "{$url}/{$name}";
            $buf = ['text' => $name];
            if (is_dir($currentPath) && $this->excludedDir($name)) {
                $buf['icon'] = "glyphicon glyphicon-folder-open";
                $buf['nodes'] = $this->readDir($currentPath, $currentUrl);
                $buf['href'] = null;
            } else {
                $buf['icon'] = "glyphicon glyphicon-file";
                $buf['selectedIcon'] = "glyphicon glyphicon-hand-right";
                $buf['href'] = $this->urlGenerator->generate('file', ['path' => $currentUrl]);
                $buf['color'] = "blue";
            }
            $result[] = $buf;
        }
        return $result;
    }

    private function findAllFiles(string $path): array
    {
        $files = $this->getDir($path);
        $result = [];
        foreach ($files as $name => $non) {
            if (!$this->excludedDir($name)) {
                continue;
            }
            $currentPath = "{$path}/{$name}";
            if (is_dir($currentPath)) {
                $result = array_merge($result, $this->findAllFiles($currentPath));
            } else {
                $result[] = $currentPath;
            }
        }
        return $result;
    }

    private function filterName(string $fullName, string $name): bool
    {
        return !$this->excludedDir($name) || (!is_dir($fullName) && !$this->onlyMdFile($fullName));
    }
}