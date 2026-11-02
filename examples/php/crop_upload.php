<?php

    $uploadBufferLevel = ob_get_level();
    ob_start();
    try {
        require_once('../qcubed.inc.php');
        //require_once(__DIR__ . '/../src/Control/CroppieHandler.php');
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
    use QCubed\Plugin\Control\CroppieHandler;

    $options = [
        'ResizeDimensions' => [320, 480, 800],
    ];

    /**
     * Handles custom operations related to image cropping and uploading using the Croppie library.
     */
    class CustomCroppieHandler extends CroppieHandler
    {
        protected function uploadInfo(): void
        {
            $path = $this->options['OriginalImageName'];
            if (!$path || !is_file($path)) $this->uploadError('Cropped image was not saved.', 500);

            $folder = Folders::loadById($this->options['FolderId']);

            if (!$folder || trim((string)$folder->getPath(), '/') !== trim($this->options['RelativePath'], '/')) {
                $this->uploadError('The crop destination has changed.', 409);
            }

            $database = Files::getDatabase();
            $database->transactionBegin();
            try {
                $file = new Files();
                $file->setFolderId($this->options['FolderId']);
                $file->setName(basename($path));
                $file->setType('file');
                $file->setPath($this->getRelativePath($path));
                $file->setDescription(null);
                $file->setExtension($this->getExtension($path));
                $file->setMimeType('image/png');
                $file->setSize(filesize($path));
                $file->setMtime(filemtime($path));
                $file->setDimensions($this->getDimensions($path));
                $file->setWidth($this->getImageWidth($path));
                $file->setHeight($this->getImageHeight($path));
                $file->save(true);
                $folder->setLockedFile(1);
                $folder->setMtime(filemtime($path));
                $folder->save();
                $database->transactionCommit();
            } catch (Throwable $error) {
                $database->transactionRollBack();
                throw $error;
            }
            parent::uploadInfo();
        }
    }

    try {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            throw new RuntimeException(json_encode(['error' => 'Only POST image uploads are accepted.']), 405);
        }

        $postLimit = max(0, ini_parse_quantity((string)ini_get('post_max_size')));

        if ($postLimit > 0 && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > $postLimit) {
            throw new RuntimeException(json_encode(['error' => 'Image request exceeds post_max_size.']), 413);
        }

        if (!isset($_POST['folderId'], $_POST['relativePath']) || !is_scalar($_POST['folderId']) ||
            filter_var($_POST['folderId'], FILTER_VALIDATE_INT) === false || !is_string($_POST['relativePath'])) {
            throw new RuntimeException(json_encode(['error' => 'Crop destination is missing.']), 400);
        }

        $folder = Folders::loadById((int)$_POST['folderId']);

        if (!$folder || trim((string)$folder->getPath(), '/') !== trim($_POST['relativePath'], '/')) {
            throw new RuntimeException(json_encode(['error' => 'Crop destination has changed. Select the folder again.']), 409);
        }
        $handler = new CustomCroppieHandler($options);
        $payload = ob_get_clean();
        json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        echo $payload;
    } catch (Throwable $error) {
        while (ob_get_level() > $uploadBufferLevel) ob_end_clean();
        $status = in_array($error->getCode(), [400, 403, 404, 405, 409, 413, 422], true) ? $error->getCode() : 500;
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        error_log('Image upload error: ' . $error->getMessage());
        error_log($error->getTraceAsString());
        $response = json_decode($error->getMessage(), true);
        echo json_encode(is_array($response) && isset($response['error']) ? $response : ['error' => 'The image could not be saved. See the server error log for details.'], JSON_UNESCAPED_UNICODE);
        exit;
    }