<?php

use QCubed\Project\Application;

class Archive
{
    private $zip;

    function __construct()
    {
        $this->zip = new ZipArchive();
    }

    /**
     * Create archive with name $filename and files $files (ABSOLUTE PATHS!)
     *
     * @param string $zipPath
     * @param array  $files
     * @param bool   $download
     *
     * @return bool
     */
    public function create(string $zipPath, array $files, bool $download = false): bool
    {
        $selectedFiles = [];

        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_dir($file)) {
                    $selectedFiles[] = $this->getAllFilesFromDirectory($file);
                } else {
                    $selectedFiles[] = $this->getAllFiles($file);
                }
            }
        }

        $success = false;

        if ($this->zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $mergedArray = array_reduce($selectedFiles, 'array_merge', array());

            foreach ($mergedArray as $file) {
                $filePath = $file['path'];

                if (file_exists($filePath)) {
                    if (is_dir($filePath)) {
                        $this->zip->addEmptyDir($file['name']);
                        $dirIterator = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($filePath),
                            RecursiveIteratorIterator::SELF_FIRST
                        );
                        foreach ($dirIterator as $subFile) {
                            $subFilePath = $subFile->getRealPath();
                            $relativePath = substr($subFilePath, strlen($filePath) + 1);

                            if ($subFile->isDir()) {
                                $this->zip->addEmptyDir($file['name'] . '/' . $relativePath);
                            } else {
                                $this->zip->addFile($subFilePath, $file['name'] . '/' . $relativePath);
                            }
                        }
                    } else {
                        $this->zip->addFile($filePath, $file['name']);
                    }
                }
            }
            $this->zip->close();

            if ($download === true) {
                $this->downloadZip($zipPath);
            }

            $success = true;
        } else {
            echo "Zip creation error";
        }
        return $success;
    }

    /**
     * Download the generated zip file
     *
     * @param string $zipPath
     */
    private function downloadZip(string $zipPath): void
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zipPath) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zipPath));
        ob_clean();
        flush();
        readfile($zipPath);
        unlink($zipPath);
        exit;
    }


    /**
     * Extract archive $zipPath to folder $destination (ABSOLUTE PATHS)
     *
     * @param string $zipPath
     * @param string $destination
     *
     * @return bool
     */
    public function unzip(string $zipPath, string $destination): bool
    {
        if ($this->zip->open($zipPath) === true) {
            $this->zip->extractTo($destination);
            $this->zip->close();
            echo "Zip file opened successfully";
            return true;
        } else {
            echo "Error opening zip file";
            return false;
        }
    }

    /**
     * Add file/folder to archive
     *
     * @param string $directory
     *
     * @return array
     */
    private function getAllFilesFromDirectory(string $directory): array
    {
        $files = [];
        $dirIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($dirIterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen(dirname($directory)) + 1);
            $files[] = [
                'path' => $filePath,
                'name' => $relativePath
            ];
        }
        return $files;
    }

    /**
     * Add file to archive
     *
     * @param string $file
     *
     * @return array[]
     */
    private function getAllFiles(string $file): array
    {
        $files = [];

        $filePath = $file;
        $relativePath = basename($file);
        $files = [[
            'path' => $filePath,
            'name' => $relativePath
        ]];

        return $files;
    }

}
