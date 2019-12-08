<?php


namespace App\Service;

use cebe\markdown\MarkdownExtra;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Service\PumlRender;


class MarkdownHelper {
    private $markdown;
    private $filePathRoot;
    private $allFiles;
    private $umlRender;

    public function __construct(FilesTree $filesHelper,
                                PumlRender $umlRender,
                                PathRepo $pathRepo) {
        $this->markdown = new MarkdownExtra();
        $this->markdown->html5 = true;
        $this->markdown->keepListStartNumber = true;

        $this->allFiles = $filesHelper->getAllFiles();

        $this->umlRender = $umlRender;
        $this->filePathRoot = $pathRepo->getFileDocPath();
    }

    public function parseFile(string $documentFullName) {
        if ($this->validatePath($documentFullName)) {
            $fileData = $this->readFile($documentFullName);
            $documentFullNameParts = pathinfo($documentFullName);
            $parseImage = $this->replaceUml($documentFullNameParts['dirname'], $fileData);
            return $this->markdown->parse($parseImage);
        }
        return 'File not found';
    }

    private function readFile(string $path): string {
        return file_get_contents("{$this->filePathRoot}{$path}");
    }

    private function validatePath(string $path) {
        $fullFileName = $this->filePathRoot . $path;
        return in_array($fullFileName, $this->allFiles) && file_exists($fullFileName);
    }

    private function replaceUml(string $documentPath, string $data): string {
        $imgPattern = '/\[\s*(?P<imageTitle>.*?)\s*\]\s*\(\s*(?P<filePath>.*\.puml\s*)\)/';
        return preg_replace_callback($imgPattern, function ($matches) use ($documentPath) {
            $fileUmlPath = "{$this->filePathRoot}{$documentPath}/{$matches['filePath']}";
            $nameFileUml = $this->umlRender->getFileName($fileUmlPath);
            return "[{$matches['imageTitle']}]({$nameFileUml})";
        }, $data);
        return $data;
    }
}