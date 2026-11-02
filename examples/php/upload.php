<?php

    $uploadBufferLevel = ob_get_level();
    ob_start();
    try {
        require_once('../qcubed.inc.php');
        //require_once(__DIR__ . '/../src/Control/VauuFileHandler.php');
    } catch (Throwable $error) {
        while (ob_get_level() > $uploadBufferLevel) ob_end_clean();
        error_log('Upload bootstrap error: ' . $error->getMessage());
        if (http_response_code() < 400) http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Failed to initialize upload handler.']);
        exit;
    }
    // Keep warnings in the log instead of corrupting the JSON response.
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ob_clean();

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Plugin\FileHandler;

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' && isset($_GET['uploadCapabilities'])) {
        while (ob_get_level() > $uploadBufferLevel) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode(FileHandler::uploadCapabilities());
        exit;
    }

    $options = array(
        'AutoOrient' => true, // Normalize derived images; preserve the original file.
        'MaxImagePixels' => 40000000, // Additional memory preflight also applies.
        //'ImageResizeQuality' => 75, // Default 90
        //'ImageResizeFunction' => 'imagecopyresized', // Default imagecopyresampled
        //'ImageResizeSharpen' => false, // Default true
        //'TempFolders' => ['thumbnail', 'medium', 'large'], // Please read the FileHandler description and manual
        //'ResizeDimensions' => [320, 480, 1500], // Please read the FileHandler description and manual
        //'DestinationPath' => null, // Please read the FileHandler description and manual
        'AcceptFileTypes' => ['gif', 'jpg', 'jpeg', 'png', 'webp', 'pdf', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'mp4', 'mov', 'svg', 'zip'], // Default null
        'DestinationPath' => !empty($_SESSION["filePath"]) ? $_SESSION["filePath"] : null, // Default null
        //'MaxFileSize' => 1024 * 1024 * 2, // 2 MB // Default null
        //'MinFileSize' => 500000, // 500 kb // Default null
        //'UploadExists' => 'overwrite', // increment || overwrite Default 'increment'
    );

    /**
     * Stores file-manager uploads in the selected folder and saves file metadata.
     * Image processing, chunk validation and temporary cleanup remain in FileHandler.
     */
    class CustomFileUploadHandler extends FileHandler
    {
        /** Save file-manager metadata atomically; output success only after commit. */
        protected function uploadInfo(): void
        {
            $fullPath = $this->options['FileName'] ?? null;
            if (($this->options['FileError'] ?? 0) != 0) {
                $this->handleError(t('File upload contains an error state.'), $fullPath);
            }
            if (!$fullPath || !is_file($fullPath)) {
                $this->handleError(t('An uploaded file isn\'t found after upload.'), $fullPath);
            }

            clearstatcache(true, $fullPath);
            $extension = $this->getExtension($fullPath);
            $mimeType = $this->getMimeType($fullPath) ?: 'application/octet-stream';
            $size = filesize($fullPath);
            $mtime = filemtime($fullPath);
            if ($size === false || $mtime === false) $this->handleError(t('Cannot read uploaded file metadata.'), $fullPath);
            $isImage = in_array($extension, self::getImageExtensions(), true);
            $dimensions = $isImage ? $this->getDimensions($fullPath) : null;
            $displaySize = $isImage && $extension !== 'svg' ? self::getDisplayDimensions($fullPath) : null;
            $width = $displaySize[0] ?? 0;
            $height = $displaySize[1] ?? 0;
            $relativePath = $this->getRelativePath($fullPath);

            $folder = Folders::loadById($_SESSION['folderId']);
            if (!$folder || trim((string)$folder->getPath(), '/') !== trim((string)$this->options['DestinationPath'], '/')) {
                $this->handleError(t('Upload destination has changed. Select the folder again.'), $fullPath);
            }

            // Encode before committing: an encoding failure must not leave a committed record.
            $payload = json_encode([
                'filename' => basename($fullPath),
                'path' => $relativePath,
                'extension' => $extension,
                'type' => $mimeType,
                'complete' => true,
                'merged' => true,
                'orientation' => $isImage ? self::getExifOrientation($fullPath) : 1,
                'displayDimensions' => $displaySize,
                'error' => 0,
                'size' => $size,
                'mtime' => $mtime,
                'dimensions' => $dimensions
            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

            $database = Files::getDatabase();
            $database->transactionBegin();
            try {
                $file = new Files();
                $file->setFolderId($_SESSION['folderId']);
                $file->setName(basename($fullPath));
                $file->setType('file');
                $file->setPath($relativePath);
                $file->setDescription(null);
                $file->setExtension($extension);
                $file->setMimeType($mimeType);
                $file->setSize($size);
                $file->setMtime($mtime);
                $file->setDimensions($dimensions);
                $file->setWidth($width);
                $file->setHeight($height);
                $file->save(true);
                $database->transactionCommit();
            } catch (Throwable $error) {
                $database->transactionRollBack();
                throw $error;
            }

            echo $payload;
        }

        /**
         * Get the width of an image file.
         *
         * @param string $path
         * @return int|string
         */
        public static function getImageWidth(string $path): mixed
        {
            if (!is_file($path)) {
                return '0';
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($ext === 'svg') {
                return '0';
            }

            $imageSize = self::getDisplayDimensions($path);

            if ($imageSize && in_array($ext, self::getImageExtensions(), true)) {
                return ($imageSize[0] ?? '0');
            }

            return '0';
        }

        /**
         * Get the height of an image file.
         *
         * @param string $path
         * @return int|string
         */
        public static function getImageHeight(string $path): mixed
        {
            if (!is_file($path)) {
                return '0';
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($ext === 'svg') {
                return '0';
            }

            $imageSize = self::getDisplayDimensions($path);

            if ($imageSize && in_array($ext, self::getImageExtensions(), true)) {
                return ($imageSize[1] ?? '0');
            }

            return '0';
        }

        /**
         * Supported image extensions.
         *
         * @return array
         */
        public static function getImageExtensions(): array
        {
            return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif', 'svg');
        }
    }

    try {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            throw new RuntimeException('Only POST uploads are accepted.');
        }
        // An empty filePath is the repository root, so do not test it with empty().
        if (empty($_SESSION['folderId']) || !array_key_exists('filePath', $_SESSION) || !is_string($_SESSION['filePath'])) {
            http_response_code(409);
            throw new RuntimeException(json_encode(['error' => 'Upload destination is missing. Select a folder in the file manager again.']));
        }
        $folder = Folders::loadById($_SESSION['folderId']);
        if (!$folder || trim((string)$folder->getPath(), '/') !== trim($_SESSION['filePath'], '/')) {
            http_response_code(409);
            throw new RuntimeException(json_encode(['error' => 'Upload destination has changed. Select the folder again.']));
        }
        $objHandler = new CustomFileUploadHandler($options);
        $payload = ob_get_clean();
        // Discard bootstrap chatter, but reject invalid response bodies.
        json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        echo $payload;
    } catch (Throwable $e) {
        while (ob_get_level() > $uploadBufferLevel) ob_end_clean();
        if (http_response_code() < 400) http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');

        error_log('Upload error: ' . $e->getMessage());
        error_log($e->getTraceAsString());

        $decoded = json_decode($e->getMessage(), true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            echo json_encode($decoded, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'error' => 'Failed to start the file/s upload system.',
                'details' => 'See the server error log for details.'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }
