<?php

    namespace QCubed\Plugin;

    use DownloadResponse;
    use JetBrains\PhpStorm\NoReturn;

    /**
     * Class Archive
     *
     * This class provides methods for creating, managing, and extracting zip archives.
     */
    class Archive
    {
        private ZipArchive $zip;
        private ?string $lastError = null;

        /**
         * Initializes the instance of the class and creates a new ZipArchive instance.
         *
         * @return void
         */
        function __construct()
        {
            $this->zip = new ZipArchive();
        }

        /**
         * Create an archive with the name $filename and files $files (ABSOLUTE PATHS!)
         *
         * @param string $zipPath
         * @param array $files
         * @param bool $download
         *
         * @return bool
         */
        public function create(string $zipPath, array $files, bool $download = false): bool
        {
            $opened = false;
            $this->lastError = null;
            try {
                if (!$files) throw new RuntimeException('No archive sources selected.');
                if (!is_dir(dirname($zipPath)) || !is_writable(dirname($zipPath))) throw new RuntimeException('Archive destination is not writable.');
                $sources = [];
                foreach ($files as $file) {
                    if (!is_string($file) || is_link($file)) throw new RuntimeException('Invalid archive source.');
                    $real = realpath($file);
                    if ($real === false || !is_readable($real)) throw new RuntimeException('An archive source is missing or unreadable.');
                    $sources[$real] = $real;
                }
                // When a parent folder is selected, do not add its selected children twice.
                $roots = array_values($sources);
                $sources = array_filter($sources, static function ($path) use ($roots) {
                    foreach ($roots as $root) if ($root !== $path && is_dir($root) && str_starts_with($path, $root . DIRECTORY_SEPARATOR)) return false;
                    return true;
                });
                $this->zip = new ZipArchive();
                $result = $this->zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                if ($result !== true) throw new RuntimeException('Cannot open archive, ZIP error ' . $result . '.');
                $opened = true;
                $usedNames = [];
                foreach ($sources as $source) {
                    $base = basename($source);
                    $name = $base;
                    $suffix = 1;
                    while (isset($usedNames[$name])) {
                        $extension = is_file($source) ? pathinfo($base, PATHINFO_EXTENSION) : '';
                        $stem = $extension === '' ? $base : substr($base, 0, -strlen($extension) - 1);
                        $name = $stem . '_' . $suffix++ . ($extension === '' ? '' : '.' . $extension);
                    }
                    $usedNames[$name] = true;
                    if (is_dir($source)) {
                        if (!$this->zip->addEmptyDir($name)) throw new RuntimeException('Cannot add archive directory.');
                        $iterator = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
                            RecursiveIteratorIterator::SELF_FIRST
                        );
                        foreach ($iterator as $item) {
                            if ($item->isLink()) throw new RuntimeException('Symbolic links are not included in download archives.');
                            $entry = $name . '/' . str_replace(DIRECTORY_SEPARATOR, '/', substr($item->getPathname(), strlen($source) + 1));
                            if ($item->isDir()) {
                                if (!$this->zip->addEmptyDir($entry)) throw new RuntimeException('Cannot add archive directory.');
                            } else {
                                $this->addArchiveFile($item->getPathname(), $entry);
                            }
                        }
                    } else {
                        $this->addArchiveFile($source, $name);
                    }
                }
                // Most compression/disk errors become visible only when closing the archive.
                $closed = $this->zip->close();
                $opened = false;
                if (!$closed) throw new RuntimeException('Cannot finalize archive. Check temporary disk space and file permissions.');
                clearstatcache(true, $zipPath);
                if (!is_file($zipPath) || filesize($zipPath) < 22) throw new RuntimeException('Archive was not created correctly.');
                if ($download) $this->downloadZip($zipPath);
                return true;
            } catch (Throwable $error) {
                $this->lastError = $error->getMessage();
                error_log('Archive creation error: ' . $this->lastError);
                if ($opened) {
                    try {
                        $this->zip->close();
                    } catch (Throwable $ignored) {
                    }
                }
                if (is_file($zipPath)) @unlink($zipPath);
                return false;
            }
        }

        /**
         * Adds a file to the archive and configures compression if necessary.
         *
         * @param string $path The file system path to the file being added.
         * @param string $name The name to be used for the file inside the archive.
         *
         * @return void
         * @throws RuntimeException If the file cannot be added to the archive or if the compression configuration
         *     fails.
         */
        private function addArchiveFile(string $path, string $name): void
        {
            if (!is_file($path) || !is_readable($path) || !$this->zip->addFile($path, $name)) throw new RuntimeException('Cannot add an archive file.');
            // These formats are already compressed; STORE avoids expensive repeated compression.
            $stored = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'zip', 'gz', '7z', 'docx', 'xlsx', 'pptx'];
            if (in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $stored, true) &&
                !$this->zip->setCompressionName($name, ZipArchive::CM_STORE)) throw new RuntimeException('Cannot configure archive compression.');
        }

        /**
         * Retrieves the last error message recorded.
         *
         * @return string|null The last error message, or null if no error has been recorded.
         */
        public function getLastError(): ?string
        {
            return $this->lastError;
        }

        /** Remove only expired archives created by the download controller, not active transfers. */
        public static function cleanupExpiredDownloads(string $directory, int $maxAge = 86400): void
        {
            foreach (glob($directory . '/archive_*.zip') ?: [] as $path) {
                if (is_link($path) || !preg_match('/^archive_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_[a-f0-9]{32}\.zip$/D', basename($path)) || filemtime($path) >= time() - $maxAge) continue;
                $stream = @fopen($path, 'rb');
                if ($stream === false) continue;
                if (flock($stream, LOCK_EX | LOCK_NB)) {
                    @unlink($path);
                    flock($stream, LOCK_UN);
                }
                fclose($stream);
            }
        }

        /**
         * Handles the downloading of a ZIP file by sending appropriate HTTP headers to the client
         * and initiating the file transfer. The file is deleted from the server after the download.
         *
         * @param string $zipPath The absolute path to the ZIP file to be downloaded.
         *
         * @return void
         */
        #[NoReturn]
        private function downloadZip(string $zipPath): void
        {
            require_once __DIR__ . '/DownloadResponse.php';
            DownloadResponse::send($zipPath, basename($zipPath), 'application/zip', true);
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
                echo "Error opening a zip file";
                return false;
            }
        }
    }