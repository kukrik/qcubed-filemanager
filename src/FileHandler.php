<?php

    namespace QCubed\Plugin;

    use Exception;
    use GdImage;
    use QCubed\QString;

    /**
     * Class FileHandler
     *
     * Note: the "upload" folder must already exist in /project/assets/ and this folder has 777 permissions.
     *
     * @property string $RootPath Default root path APP_UPLOADS_DIR. You may change the location of the file repository
     *                             at your own risk.*
     * Note: If you want to change TempPath, TempUrl and StoragePath, you have to rewrite the setup() function in the FileUpload class.
     * This class is located in the /project/includes/plugins folder.
     * @property string $TempPath = Default temp path APP_UPLOADS_TEMP_DIR. If necessary, the temp dir must be specified.
     * @property string $StoragePath Default dir named _files. This dir is generated together with the dirs
     *                               /thumbnail, /medium, /large when the corresponding page is opened for the first time.
     * @property string $FullStoragePath Please a see the setup() function! This can only be changed in this function.
     *
     * @property integer $ThumbnailResizeDimensions Default resized image dimensions. Default 320 is a good balance between
     *                                              visible quality and file size.
     * @property integer $MediumResizeDimensions Default 480. Resize image dimensions for high-density (retina) screens.
     *                                           This allows you to serve higher quality images for HiDPI screens, at the
     *                                           cost of slightly larger file size. For example, generated for site preview.
     * @property integer $LargeResizeDimensions Default 1500. Resize image dimensions for high-density (retina) screens.
     *                                          This allows you to serve higher quality images for HiDPI (e.g., 27- and 30-inch
     *                                          monitors) screens, at the cost of slightly larger file size.*
     *
     * @property integer $ImageResizeQuality Default 90. JPG compression level for resized images.
     *
     * @property integer $PngLevel Default 6. PNG compression level for resized images.
     *                               Acceptable values are 0 (no compression, largest file size, fastest write)
     *                              through 9 (maximum compression, smallest file size, slowest write).
     *                              PNG is a lossless format, so compression does not reduce image quality,
     *                              but higher compression values mean smaller files at the cost of performance.
     *                              The PHP default is 6, which usually gives a good balance between file size and speed.
     *
     *                              Use a lower value like 0–3 if you prioritize faster image generation
     *                              and can accept larger files (e.g., for temporary images, internal use, or very fast servers).
     *                              Use a higher value like 7–9 if disk space or network transfer size is more important,
     *                              and slower save speed is acceptable (e.g., for long-term storage or many images).
     *                              In most web projects, a value between 5 and 7 is recommended.
     *
     * @property string $ImageResizeFunction Default 'imagecopyresampled'. Choose between 'imagecopyresampled' (smoother)
     *                                       and 'imagecopyresized' (faster). The difference is minimal, but you could use
     *                                       imagecopyresized, for example if you want faster resizing when not using image
     *                                       resize cache.
     * @property boolean $ImageResizeSharpen Default true. Creates sharper (less blurry) preview images.
     * @property array $TempFolders Default '['thumbnail', 'medium', 'large']'. If you want to change the names of
     *                              the temporary folders, you need to override the setup() function of the FileUpload class
     *                              ($strCreateDirs = ['/thumbnail', '/medium', '/large'];).
     *
     * @property array $ResizeDimensions Default '[320, 480, 1500]'. Note: Here you need to set the ResizeDimensions
     *                                   [320, 480, 1500] in the order of TempFolders ['thumbnail', 'medium', 'large'].
     *
     *                                   Default 320 is a good balance between visible quality and file size.
     *
     *                                   Default 480. Resize image dimensions for high-density (retina) screens. This allows
     *                                   you to serve higher quality images for HiDPI screens, at the cost of slightly
     *                                   larger file size. For example, generated for site preview.
     *
     *                                   Default 1500. Resize image dimensions for high-density (retina) screens. This allows
     *                                   you to serve higher quality images for HiDPI (e.g., 27- and 30-inch monitors) screens,
     *                                   at the cost of slightly larger file size.
     *
     * @property array $AcceptFileTypes Default null. The output form of the array looks like this:
     *                                  '['gif', 'jpg', 'jpeg', 'png', 'pdf']'. If necessary, specify the allowed file types.
     *                                  When empty (default), all file types are allowed.
     * @property integer $MaxFileSize Default null. Sets the maximum file size (bytes) allowed for uploads. Default value null
     *                                it means no limit, but the maximum file size will always be limited by your server's
     *                                PHP upload_max_filesize value.
     *
     * @property integer $MinFileSize Default 1. If necessary, you can limit the minimum bytes of the uploaded image in order
     *                                not to degrade the quality of the image processing.
     * @property string $UploadExists Default 'increment'. Decides what to do if the uploaded filename already exists in upload
     *                                target folder. Default 'increment' will rename uploaded files by appending a number,
     *                                'overwrite' will overwrite existing files.
     *                                Usage:
     *                                $this->UploadExists = 'increment'; // increment filename, for example, filename.jpg => filename-2.jpg
     *                                $this->UploadExists = 'overwrite', // overwrite an existing file if the filename exists
     *
     * @property-read string $FileName is the name of the file that the user uploads?
     * @property-read string $FileType is the MIME type of the file?
     * @property-read integer $FileSize is the size in bytes of the file?
     * @property string $DestinationPath Default null. This is a prepared option. If there is a need to create new subfolders
     *                                   and save images there. Then you need to make your own function to create new folders.
     *                                   For example,
     *                                   [folder1]
     *                                   |___ [folder2]
     *                                        |___ [folder3]
     *                                   Then write $this->DestinationPath = 'folder1/folder2/folder3' etc...
     *
     * @package QCubed\Plugin
     */

    class FileHandler
    {
        protected array $options;
        // PHP File Upload error message codes:
        // https://www.php.net/manual/en/features.file-upload.errors.php
        protected array $uploadErrors;
        protected string $chunkEnabled;
        protected int $index;
        protected int $chunk;
        protected string $uploadId;
        protected int $uploadSize;
        protected int $count;
        protected int $counter = 0;

        /**
         * Constructor method for initializing upload options and handling file uploads.
         *
         * @param array|null $options An optional array to override default options. Example keys include:
         *                             - 'RootPath': Directory where files are stored.
         *                             - 'TempPath': Directory where temporary files are stored.
         *                             - 'StoragePath': Subdirectory for file storage.
         *                             - 'ImageResizeQuality': Quality of resized images.
         *                             - 'PngLevel Default 6. PNG compression level for resized images.
         *                             - 'ImageResizeFunction': Function used for an image resizing.
         *                             - 'AcceptFileTypes': Allowed file types for uploads.
         *                             - 'MaxFileSize': Maximum file size for uploads.
         *                             - 'MinFileSize': Minimum file size for uploads.
         *                             - Other configuration options related to uploads.
         *
         * @return void
         * @throws \Throwable
         * @throws Exception
         */
        public function __construct(?array $options = null)
        {
            $this->options = array(
                'RootPath' => APP_UPLOADS_DIR,
                'TempPath' => APP_UPLOADS_TEMP_DIR,
                'StoragePath' => '_files',
                'FullStoragePath' => null,
                'ChunkPath' => null,

                'ImageResizeQuality' => 90,
                'AutoOrient' => true,
                'MaxImagePixels' => 40000000,
                'PngLevel' => 6,
                'ImageResizeFunction' => 'imagecopyresampled', // imagecopyresampled || imagecopyresized
                'ImageResizeSharpen' => true,

                'TempFolders' =>  ['thumbnail', 'medium', 'large'],
                'ResizeDimensions' => [320, 480, 1500],
                'DestinationPath' => null,
                'AcceptFileTypes' => null,
                'MaxFileSize' => null,
                'MinFileSize' => 1,
                'UploadExists' => 'increment', // increment || overwrite

                'File' => null,
                'FileName' => null,
                'FileType' => null,
                'FileSize' => null,
                'FileError' => null,
            );

            $this->uploadErrors = array(
                1 => t('Uploaded file exceeds upload_max_filesize directive in php.ini'),
                2 => t('An uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form'),
                3 => t('The uploaded file was only partially uploaded'),
                4 => t('No file was uploaded'),
                6 => t('Missing a temporary folder'),
                7 => t('Failed to write a file to disk'),
                8 => t('A PHP extension stopped the file upload'),
                //'post_max_size' => t('The an uploaded file exceeds the post_max_size directive in php.ini'),
                'max_file_size' => t('File is too big'),
                'min_file_size' => t('File is too small'),
                'accept_file_types' => t('Filetype not allowed'),
                'invalid_image_type' => t('Invalid image type'),
                'invalid_file_size' => t('Invalid file size'),
                'post_max_size' => t('File size exceeds max filesize %s'),
                'overwritten' => t('This file has been overwritten'),
                'invalid_image' => t('Invalid image / failed getimagesize()'),
                'failed_to_resize_image' => t('Failed to resize image'),
                'resizeimage_failed_to_create_and_resize_the_image' => t('The resizeImage() function failed to create and resize the image'),
                'invalid_chunk_size' => t('Invalid chunk size'),
                'failed_to_open_stream' => t('Failed to open stream: No such directory to put into'),
                'could-not_write_output' => t('Failed to open the output stream'),
                'could_not_read_input' => t('Failed to open the input stream'),
                'failed_to_move_uploaded_file' => t('Failed to move an uploaded file'),
                'file_not_found' => t('File not found'),
                'failed_to_create_image' => t('Failed to create GD image'),
                'failed_to_load_image' => t('Failed to load image with GD'),

                'upload_request_exceeds_post_max_size' => t('Upload request exceeds post_max_size ('),
                'invalid_filename' => t('Invalid filename.'),
                'invalid_destination_path' => t('Invalid destination path.'),
                'invalid_chunk_metadata' => t('Invalid chunk metadata.'),
                'cannot_create_upload_temporary_directory' => t('Cannot create upload temporary directory.'),
                'this_upload_has_already_completed_start_new_upload' => t('This upload has already completed. Start a new upload.'),
                'cannot_lock_upload' => t('Cannot lock upload.'),
                'upload_must_start_with_chunk_1' => t('Upload must start with chunk 1.'),
                'upload_context_or_chunk_sequence_changed_start_new_upload' => t('Upload context or chunk sequence changed. Start a new upload.'),
                'cannot_save_upload_chunk' => t('Cannot save upload chunk.'),
                'invalid_chunk_byte_count' => t('Invalid chunk byte count.'),
                'invalid_total_upload_size' => t('Invalid total upload size.'),
                'incomplete_upload' => t('Incomplete upload.'),
                'cannot_open_merged_upload' => t('Cannot open merged upload.'),
                'missing_upload chunk' => t('Missing upload chunk.'),
                'incomplete_chunk_write' => t('Incomplete chunk write.'),
                'cannot_flush_merged_upload' => t('Cannot flush merged upload.'),
                'invalid_merged_file_size' => t('Invalid merged file size.'),
                'cannot_lock_upload_destination' => t('Cannot lock upload destination.'),
                'cannot_move_merged_upload' => t('Cannot move merged upload.'),

                'cannot_save_upload_state' => t('Cannot save upload state.'),
                'image_folders_and_dimensions_must_have_equal_counts' => t('Image folders and dimensions must have equal counts.'),
                'invalid_image_output_configuration' => t('Invalid image output configuration.'),
                'cannot_create_image_output_directory' => t('Cannot create image output directory.'),
                'cannot_copy_image' => t('Cannot copy image.'),
                'cannot_rotate_image' => t('Cannot rotate image.'),
                'cannot_mirror_image' => t('Cannot mirror image.'),

                'image_content_does_not_match_its_extension' => t('Image content does not match its extension.'),
                'image_resolution_exceeds_the_allowed_pixel_count' => t('Image resolution exceeds the allowed pixel count.'),
                'not_enough_server_memory_to_process_this_image_resolution' => t('Not enough server memory to process this image resolution.'),
                'cannot_save_resized_image' => t('Cannot save resized image.')
            );

            if ($options) {
                $this->options = array_merge($this->options, $options);
            }

            $this->options['FullStoragePath'] = $this->options['TempPath'] . '/' . $this->options['StoragePath'];
            $this->options['ChunkPath'] = $this->options['FullStoragePath'] . '/' . 'temp';

            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                $this->header();
                $this->handleFileUpload();
            }
        }

        /**
         * Sends HTTP headers to prevent caching and define the content type as JSON.
         *
         * This method ensures that the client does not cache the response by setting
         * appropriate headers. It also specifies the content type of the response
         * as JSON.
         *
         * @return void
         */
        protected function header(): void
        {
            // Make sure a file is not cached (as it happens, for example, on iOS devices)
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/json');
        }

        /**
         * Handles a file upload process, including regular and chunked uploads, and validates the uploaded file.
         *
         * This method processes uploaded files by checking for chunked uploads,
         * validating file attributes, detecting duplicate file names, and saving
         * the file to the specified destination. It supports features such as file
         * name incrementation and error handling.
         *
         * - The method determines if the upload is chunk-enabled and processes accordingly.
         * - For regular uploads, it validates the file, handles duplicate filenames, and uploads the file.
         * - For chunked uploads, it processes and saves the file in parts.
         *
         * @return void
         * @throws Exception
         * @throws \Throwable
         */
        public function handleFileUpload(): void
        {
            $postLimit = self::configBytes(ini_get('post_max_size'));
            if ($postLimit > 0 && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > $postLimit) {
                http_response_code(413);
                $this->handleError('upload_request_exceeds_post_max_size' . ini_get('post_max_size') . ').');
            }
            $input = $_FILES['files'] ?? null;
            if (!is_array($input) || !isset($input['name'], $input['tmp_name'], $input['error']) ||
                !is_string($input['name']) || !is_string($input['tmp_name']) || !is_int($input['error'])) {
                $this->handleError($this->getErrorMessage('file_not_found'));
            }
            $name = basename(str_replace('\\', '/', $input['name']));
            if ($name === '' || $name === '.' || $name === '..' || str_contains($name, "\0")) {
                $this->handleError('invalid_filename');
            }
            $name = self::sanitizeForFile($name);
            $this->options['FileSize'] = (int)($input['size'] ?? 0);
            $this->options['FileError'] = $input['error'];
            $this->options['File'] = $input['tmp_name'];
            if ($input['error'] !== UPLOAD_ERR_OK) {
                if ($input['error'] === UPLOAD_ERR_INI_SIZE) http_response_code(413);
                $this->handleError($this->getErrorMessage($input['error']), $name);
            }
            $destination = trim((string)$this->options['DestinationPath'], '/');
            if (str_contains($destination, '\\') || preg_match('~(^|/)\.\.?(/|$)~', $destination)) {
                $this->handleError('invalid_destination_path', $name);
            }
            $file = rtrim($this->options['RootPath'], '/') . '/' . ($destination === '' ? '' : $destination . '/') . $name;
            $this->options['FileName'] = $file;
            if (!is_dir(dirname($file)) || !is_uploaded_file($input['tmp_name'])) {
                $this->handleError($this->getErrorMessage('file_not_found'), $name);
            }
            $this->chunkEnabled = ($_POST['chunkEnabled'] ?? 'false') === 'true' ? 'true' : 'false';
            $this->options['FileType'] = self::getMimeType($input['tmp_name']) ?: 'application/octet-stream';
            if ($this->chunkEnabled === 'false') {
                $newName = $this->checkDuplicateFile($file);
                if ($newName !== null) $this->options['FileName'] = $newName;
                $this->regularValidate($input['tmp_name'], $this->options['FileName'], $input['error']);
                $this->handleRegularUpload($input['tmp_name'], $this->options['FileName']);
                return;
            }
            foreach (['chunk', 'count', 'index', 'totalSize'] as $key) {
                if (!isset($_POST[$key]) || !is_scalar($_POST[$key]) ||
                    filter_var($_POST[$key], FILTER_VALIDATE_INT) === false) {
                    $this->handleError('invalid_chunk_metadata', $name);
                }
            }
            $this->chunk = (int)$_POST['chunk'];
            $this->count = (int)$_POST['count'];
            $this->index = (int)$_POST['index'];
            $this->uploadSize = (int)$_POST['totalSize'];
            $id = $_POST['uploadId'] ?? '';
            if (!is_string($id) || !preg_match('/^[a-zA-Z0-9_-]{16,80}$/D', $id) ||
                $this->count < 1 || $this->count > 100000 || $this->chunk < 1 || $this->chunk > $this->count ||
                $this->index < 0 || $this->uploadSize < 1 || $this->index >= $this->uploadSize ||
                $this->options['FileSize'] < 1 || $this->options['FileSize'] > $this->uploadSize - $this->index) {
                $this->handleError('invalid_chunk_metadata', $name);
            }
            if ($this->options['MaxFileSize'] && $this->uploadSize > $this->options['MaxFileSize']) {
                $this->handleError($this->getErrorMessage('max_file_size'), $name);
            }
            $this->uploadId = $id;
            $this->handleChunkUpload($input['tmp_name'], $file);
        }

        /**
         * Read PHP shorthand limits without changing the server configuration.
         * Converts a human-readable size value (e.g., "256M", "2G") into bytes.
         * Handles optional unit suffixes and powers of 1024 for various size units.
         *
         * @param mixed $value The size value to be converted. Can include suffixes such as K, M, G, T, or P.
         *
         * @return int The size value in bytes, or 0 if the input is invalid or unconvertible.
         */
        public static function configBytes(mixed $value): int
        {
            $value = trim((string)$value);
            if ($value === '-1' || $value === '') return 0;
            if (!preg_match('/^(\d+(?:\.\d+)?)\s*([KMGTP]?)(?:B)?$/iD', $value, $match)) return 0;
            $power = strpos(' KMGTP', strtoupper($match[2]));
            return (int)min(PHP_INT_MAX, (float)$match[1] * (1024 ** $power));
        }

        /**
         * Determines and returns the upload capabilities of the system, including maximum
         * chunk size, protocol version, and features such as EXIF support and upload enablement.
         *
         * @return array An associative array containing the following keys:
         *               - 'error' (null): Placeholder for potential errors, currently always null.
         *               - 'protocol' (int): The protocol version used for uploads.
         *               - 'maxChunkSize' (int): The maximum allowable size for upload chunks in bytes.
         *               - 'uploadsEnabled' (bool): Indicates whether file uploads are enabled in the system configuration.
         *               - 'exifAvailable' (bool): Indicates whether the EXIF extension is available in PHP.
         */
        public static function uploadCapabilities(): array
        {
            $upload = self::configBytes(ini_get('upload_max_filesize'));
            $post = self::configBytes(ini_get('post_max_size'));
            // Allow room for multipart boundaries, filename and metadata fields.
            $limits = [1024 * 1024];
            if ($upload > 0) $limits[] = $upload;
            if ($post > 0) $limits[] = max(0, $post - 65536);
            return ['error' => null, 'protocol' => 2, 'maxChunkSize' => min($limits),
                'uploadsEnabled' => filter_var(ini_get('file_uploads'), FILTER_VALIDATE_BOOLEAN),
                'exifAvailable' => function_exists('exif_read_data')];
        }

        /**
         * Validates an uploaded file against defined size and type constraints.
         *
         * @param mixed $uploadedFile The uploaded file data to be validated.
         * @param string $fileName The name of the uploaded file.
         * @param int $error Error code associated with the file upload process.
         *
         * @return bool|null Returns true if the file passes all validation checks, false otherwise.
         * @throws \Exception
         */
        public function regularValidate(mixed $uploadedFile, string $fileName, int $error): ?bool
        {
            if ($error) {
                $this->handleError($this->getErrorMessage($error), $fileName);
            }

            // Get the value of post_max_size in bytes
            $postMaxSize = $this->getConfigBytes(ini_get('post_max_size'));

            // Check if the file size exceeds the post_max_size limit
            if ($postMaxSize && ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > $postMaxSize)) {
                $this->handleError($this->getErrorMessage('post_max_size'), $fileName);
            }

            // Check file size
            $fileSize = $this->getFileSize($uploadedFile);

            if (!empty($this->options['MaxFileSize']) && $fileSize > $this->options['MaxFileSize']) {
                $this->handleError($this->getErrorMessage('max_file_size'), $fileName);
            }

            if (!empty($this->options['MinFileSize']) && $fileSize < $this->options['MinFileSize']) {
                $this->handleError($this->getErrorMessage('min_file_size'), $fileName);
            }

            if (!empty($this->options['AcceptFileTypes'])) {
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($ext, $this->options['AcceptFileTypes'], true)) {
                    $this->handleError($this->getErrorMessage('accept_file_types'), $fileName);
                }
            }

            return true;
        }

        /**
         * Validates a file chunk or the final merged file based on various conditions such as file size, type, or errors.
         *
         * @param mixed $uploadedFile The currently uploaded chunk file being validated.
         * @param string $fileName The name or path of the file being processed.
         * @param int $error An error code indicating issues with the file upload process.
         * @param bool $isLastChunk Indicates if this is the last chunk of the file being uploaded.
         *
         * @return bool|null Returns true if the validation is successful, null if no validation is performed, or throws an error otherwise.
         * @throws Exception
         */

        public function chunkValidate(mixed $uploadedFile, string $fileName, int $error, bool $isLastChunk): ?bool
        {
            if ($error) {
                $this->handleError($this->getErrorMessage($error), $fileName);
            }

            // If this is the last piece, we can check the size and type of the final file
            if ($isLastChunk) {
                $fileSize = $this->getFileSize($fileName);

                if (!empty($this->options['MaxFileSize']) && $fileSize > $this->options['MaxFileSize']) {
                    $this->handleError($this->getErrorMessage('max_file_size'), $fileName);
                }

                if (!empty($this->options['MinFileSize']) && $fileSize < $this->options['MinFileSize']) {
                    $this->handleError($this->getErrorMessage('min_file_size'), $fileName);
                }

                if (!empty($this->options['AcceptFileTypes'])) {
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    if (!in_array($ext, $this->options['AcceptFileTypes'], true)) {
                        $this->handleError($this->getErrorMessage('accept_file_types'), $fileName);
                    }
                }
            }

            // If you want to check anything about the chunk itself (like the chunk size), add that logic here.
            // Currently, the size or type of regular chunks is not checked - only after all the chunks are merged.
            return true;
        }

        /**
         * Handles the upload of a regular file by moving it to the target location,
         * clearing the file status cache, performing any necessary image resizing, and
         * updating relevant upload information.
         *
         * @param string $uploadedFile The temporary file path of the uploaded file.
         * @param string $file The target file path where the uploaded file will be moved.
         *
         * @return void
         * @throws Exception
         * @throws \Throwable
         */
        protected function handleRegularUpload(string $uploadedFile, string $file): void
        {
            if (!is_uploaded_file($uploadedFile)) {
                $this->handleError($this->getErrorMessage('file_not_found'), $file);
            }

            $targetDir = dirname($file);
            if (!is_dir($targetDir)) {
                $this->handleError($this->getErrorMessage('failed_to_open_stream'), $file);
            }

            if (!move_uploaded_file($uploadedFile, $file)) {
                $this->handleError($this->getErrorMessage('failed_to_move_uploaded_file'), $file);
            }

            clearstatcache(true, $file);

            if (!is_file($file)) {
                $this->handleError($this->getErrorMessage('file_not_found'), $file);
            }

            $this->options['FileSize'] = filesize($file);

            try {
                $this->options['FileType'] = self::getMimeType($file) ?: 'application/octet-stream';
                $this->resizeImageProcess($file);
                $this->uploadInfo();
            } catch (\Throwable $error) {
                $this->removeDerivedFiles($file);
                if ($this->options['UploadExists'] !== 'overwrite' && is_file($file)) unlink($file);
                throw $error;
            }
        }

        /**
         * Handles the uploading and processing of file chunks, merging them into a complete file.
         *
         * @param string $uploadedFile The path to the currently uploaded chunk file.
         * @param string $file The full path of the final intended file after all chunks are merged.
         *
         * @return void
         * @throws Exception
         * @throws \Throwable
         */
        protected function handleChunkUpload(string $uploadedFile, string $file): void
        {
            $root = $this->options['ChunkPath'];
            if (!is_dir($root) && !mkdir($root, 0700, true) && !is_dir($root)) {
                $this->handleError('cannot_create_upload_temporary_directory', $file);
            }
            // Remove completed legacy directories and expired abandoned uploads on new files.
            if ($this->chunk === 1) $this->cleanupExpiredUploads();
            // Bind temporary storage to the PHP session, not a client-supplied directory name.
            $owner = hash('sha256', session_id() . '|' . $this->options['RootPath']);
            $receipt = $owner . '-' . $this->uploadId;
            if (isset($_SESSION['galleryCompletedUploads'][$receipt])) {
                $this->handleError('this_upload_has_already_completed_start_new_upload', $file);
            }
            $directory = $root . '/upload-' . $owner . '-' . $this->uploadId;
            if (!is_dir($directory) && !mkdir($directory, 0700) && !is_dir($directory)) {
                $this->handleError('cannot_create_upload_temporary_directory', $file);
            }
            $lock = fopen($directory . '/lock', 'c');
            if ($lock === false || !flock($lock, LOCK_EX)) {
                if (is_resource($lock)) fclose($lock);
                $this->handleError('cannot_lock_upload', $file);
            }
            $completed = false;
            try {
                $metadataFile = $directory . '/state.json';
                $state = is_file($metadataFile) ? json_decode(file_get_contents($metadataFile), true, 512, JSON_THROW_ON_ERROR) : null;
                if ($state === null) {
                    if ($this->chunk !== 1 || $this->index !== 0) $this->handleError('upload_must_start_with_chunk_1', $file);
                    $state = ['file' => $file, 'count' => $this->count, 'totalSize' => $this->uploadSize,
                        'context' => [$this->options['DestinationPath'], $_SESSION['id'] ?? null, $_SESSION['groupId'] ?? null, $_SESSION['folderId'] ?? null],
                        'nextChunk' => 1, 'received' => 0, 'finished' => false];
                }
                $context = [$this->options['DestinationPath'], $_SESSION['id'] ?? null, $_SESSION['groupId'] ?? null, $_SESSION['folderId'] ?? null];
                if ($state['file'] !== $file || $state['count'] !== $this->count || $state['totalSize'] !== $this->uploadSize ||
                    $state['context'] !== $context || $state['finished'] || $state['nextChunk'] !== $this->chunk || $state['received'] !== $this->index) {
                    $this->handleError('upload_context_or_chunk_sequence_changed_start_new_upload', $file);
                }
                $part = $directory . '/part-' . $this->chunk;
                if (!move_uploaded_file($uploadedFile, $part)) $this->handleError('cannot_save_upload_chunk', $file);
                clearstatcache(true, $part);
                $bytes = filesize($part);
                if ($bytes !== $this->options['FileSize']) $this->handleError('invalid_chunk_byte_count', $file);
                $state['received'] += $bytes;
                $state['nextChunk']++;
                if ($this->chunk < $this->count) {
                    if ($state['received'] >= $this->uploadSize) $this->handleError('invalid_total_upload_size', $file);
                    $this->writeUploadState($metadataFile, $state);
                    echo json_encode(['filename' => basename($file), 'error' => null, 'merged' => false,
                        'chunk' => $this->chunk, 'count' => $this->count, 'receivedBytes' => $state['received'], 'uploadId' => $this->uploadId]);
                    return;
                }
                if ($state['received'] !== $this->uploadSize) $this->handleError('incomplete_upload', $file);
                $merged = $directory . '/' . basename($file);
                $output = fopen($merged, 'wb');
                if ($output === false) $this->handleError('cannot_open_merged_upload', $file);
                try {
                    for ($number = 1; $number <= $this->count; $number++) {
                        $path = $directory . '/part-' . $number;
                        $input = fopen($path, 'rb');
                        if ($input === false) $this->handleError('missing_upload chunk', $file);
                        try {
                            if (stream_copy_to_stream($input, $output) !== filesize($path)) $this->handleError('incomplete_chunk_write', $file);
                        } finally { fclose($input); }
                    }
                    if (!fflush($output)) $this->handleError('cannot_flush_merged_upload', $file);
                } finally { fclose($output); }
                clearstatcache(true, $merged);
                if (filesize($merged) !== $this->uploadSize) $this->handleError('invalid_merged_file_size', $file);
                $this->chunkValidate($merged, $merged, UPLOAD_ERR_OK, true);
                // Finalization is single-use, even if image processing or persistence later fails.
                $state['finished'] = true;
                $this->writeUploadState($metadataFile, $state);
                // Serialize filename allocation in the destination, including different upload IDs.
                $targetLock = fopen($root . '/destination-' . hash('sha256', dirname($file)) . '.lock', 'c');
                if ($targetLock === false || !flock($targetLock, LOCK_EX)) {
                    if (is_resource($targetLock)) fclose($targetLock);
                    $this->handleError('cannot_lock_upload_destination', $file);
                }
                try {
                    $target = $this->checkDuplicateFile($file) ?? $file;
                    if (!rename($merged, $target)) $this->handleError('cannot_move_merged_upload', $file);
                    $this->options['FileName'] = $target;
                    $this->options['FileSize'] = $this->uploadSize;
                    $this->options['FileType'] = self::getMimeType($target) ?: 'application/octet-stream';
                    try {
                        $this->resizeImageProcess($target);
                        $this->uploadInfo();
                    } catch (\Throwable $error) {
                        $this->removeDerivedFiles($target);
                        // Increment mode creates a new original; never remove an existing overwrite target.
                        if ($this->options['UploadExists'] !== 'overwrite' && is_file($target)) unlink($target);
                        throw $error;
                    }
                } finally { flock($targetLock, LOCK_UN); fclose($targetLock); }
                $this->rememberCompletedUpload($receipt);
                $completed = true;
            } finally {
                if ($completed || ($state['finished'] ?? false)) {
                    // Finalized failures are not resumable; discard their parts as well.
                    // Cleanup errors must not turn an already committed upload into HTTP 500.
                    $this->removeUploadDirectory($directory);
                } else {
                    touch($directory);
                }
                flock($lock, LOCK_UN);
                fclose($lock);
            }
        }

        /**
         * Tracks and stores information about a completed upload in the session to prevent duplicate handling.
         *
         * @param string $receipt A unique identifier for the completed upload.
         *
         * @return void
         */
        protected function rememberCompletedUpload(string $receipt): void
        {
            $receipts = $_SESSION['galleryCompletedUploads'] ?? [];
            $receipts = array_filter($receipts, static fn($time) => is_int($time) && $time >= time() - 86400);
            $receipts[$receipt] = time();
            $_SESSION['galleryCompletedUploads'] = array_slice($receipts, -4096, null, true);
        }

        /**
         * Removes the specified upload directory and its contents, including files and subdirectories.
         * If any files or the directory itself cannot be deleted, it logs the related errors.
         *
         * @param string $directory The path of the upload directory to be removed.
         *
         * @return void
         */
        protected function removeUploadDirectory(string $directory): void
        {
            try {
                foreach (glob($directory . '/*') ?: [] as $path) {
                    if (is_file($path) && !is_link($path) && !@unlink($path)) {
                        error_log('Upload temporary file cleanup failed: ' . $path);
                    }
                }
                if (is_dir($directory) && !@rmdir($directory)) error_log('Upload temporary directory cleanup failed: ' . $directory);
            } catch (\Throwable $error) {
                error_log('Upload cleanup error: ' . $error->getMessage());
            }
        }

        /**
         * Writes the upload state to a specified file in JSON format, ensuring atomicity with file locking.
         *
         * @param string $path The file path where the upload state will be saved.
         * @param array $state The upload state data to be serialized and stored.
         *
         * @return void
         * @throws \JsonException
         */
        protected function writeUploadState(string $path, array $state): void
        {
            $json = json_encode($state, JSON_THROW_ON_ERROR);
            if (file_put_contents($path, $json, LOCK_EX) !== strlen($json)) $this->handleError('cannot_save_upload_state');
        }

        /**
         * Cleans up expired or completed upload directories based on their last activity timestamps or state.
         * It checks for expired uploads, removes directories if necessary, and records completed uploads.
         *
         * @param int $maxAge The maximum age (in seconds) of an upload directory before it is considered expired. Defaults to 86400 seconds (24 hours).
         *
         * @return void
         */
        public function cleanupExpiredUploads(int $maxAge = 86400): void
        {
            foreach (glob($this->options['ChunkPath'] . '/upload-*', GLOB_ONLYDIR) ?: [] as $directory) {
                $lastActivity = filemtime($directory);
                if (is_link($directory)) continue;
                $lock = fopen($directory . '/lock', 'c');
                if ($lock === false) continue;
                if (flock($lock, LOCK_EX | LOCK_NB)) {
                    $statePath = $directory . '/state.json';
                    clearstatcache(true, $statePath);
                    if (is_file($statePath)) $lastActivity = max($lastActivity, filemtime($statePath));
                    $state = is_file($statePath) ? json_decode(file_get_contents($statePath), true) : null;
                    // Older successful uploads already deleted all part-N files but kept lock/state.
                    $finished = is_array($state) && ($state['finished'] ?? false) === true &&
                        ($state['received'] ?? null) === ($state['totalSize'] ?? null) &&
                        !glob($directory . '/part-*') && count(glob($directory . '/*') ?: []) === 2;
                    if ($lastActivity < time() - $maxAge || $finished) {
                        $owner = hash('sha256', session_id() . '|' . $this->options['RootPath']);
                        $prefix = 'upload-' . $owner . '-';
                        if ($finished && str_starts_with(basename($directory), $prefix)) {
                            $this->rememberCompletedUpload(substr(basename($directory), strlen('upload-')));
                        }
                        $this->removeUploadDirectory($directory);
                    }
                    flock($lock, LOCK_UN);
                }
                fclose($lock);
            }
        }

        /**
         * Deletes temporary files created during the file chunk upload process and clears related session data.
         *
         * @param array $tempFiles An array of paths to the temporary files to be deleted.
         *
         * @return void
         */
        protected function partFilesToDelete(array $tempFiles): void
        {
            foreach ($tempFiles as $tempFile) {
                unlink($tempFile);
            }

            unset($_SESSION['parts']);
        }

        /**
         * Checks for duplicate files in the specified directory and handles them based on the upload configuration.
         *
         * If a file with the same name already exists, the behavior is determined by the 'UploadExists' option:
         * - If set to 'overwrite', the method overwrites the existing file.
         * - If set to 'increment', the method assigns a unique incremented filename to the new file.
         *
         * @param string $fileName The full path of the file to check for duplicates.
         *
         * @return string|null Returns the original or newly generated filename if a duplicate is handled,
         *                     or null if the file is overwritten.
         * @throws \Exception
         */
        protected function checkDuplicateFile(string $fileName): ?string
        {
            // Set dirname, name and ext
            $dirname = $this->removeFileName($fileName);
            $name = pathinfo($fileName, PATHINFO_FILENAME);
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);

            $isExists = false;
            $files = glob($dirname . '/*', GLOB_NOSORT);

            if (in_array($fileName, $files)) {
                $isExists = true;
            }

            if ($isExists === true) {
                if (file_exists($dirname . '/' . $name . '.' . $ext)) {

                    // If 'overwrite' is selected, the file will be overwritten
                    if ($this->options['UploadExists'] == 'overwrite') {
                        $this->handleError($this->getErrorMessage('overwritten'), $name);
                        return null;
                    }

                    // If 'increment' is selected, the filename is incremented
                    if ($this->options['UploadExists'] == 'increment') {
                        $inc = 1;
                        while (file_exists($dirname . '/' . $name . '-' . $inc . '.' . $ext)) {
                            $inc++;
                        }
                        return $dirname . '/' . $name . '-' . $inc . '.' . $ext;
                    }
                }

                return $fileName;
            }

            return null;
        }

        /**
         * Retrieves a user-friendly error message for a given error code or returns the original error if no message is available.
         *
         * @param mixed $error The error code or message to retrieve a corresponding user-friendly error message for.
         *
         * @return mixed
         */
        protected function getErrorMessage(mixed $error): mixed
        {
            return $this->uploadErrors[$error] ?? $error;
        }

        /**
         * Processes and resizes an image file based on specified dimensions for different temporary folders.
         *
         * @param string $fileName The path to the image file to be processed and resized.
         *
         * @return void
         * @throws Exception
         */
        protected function resizeImageProcess(string $fileName): void
        {
            $folders = $this->options['TempFolders'];
            $dimensions = $this->options['ResizeDimensions'];
            if (count($folders) !== count($dimensions)) $this->handleError('image_folders_and_dimensions_must_have_equal_counts', $fileName);
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($extension, self::getImageExtensions(), true)) return;
            $created = [];
            // Turn GD/filesystem warnings into catchable failures, allowing the upload
            // rollback to remove the new original and any partial derivatives.
            set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            }, E_WARNING | E_NOTICE);
            try {
                foreach (array_combine($folders, $dimensions) as $folder => $dimension) {
                    if (!is_string($folder) || !preg_match('/^[a-zA-Z0-9_-]+$/D', $folder) || (int)$dimension < 1) {
                        $this->handleError('invalid_image_output_configuration', $fileName);
                    }
                    $target = $this->options['FullStoragePath'] . '/' . $folder . '/';
                    if ($this->options['DestinationPath']) $target .= trim($this->options['DestinationPath'], '/') . '/';
                    if (!is_dir($target) && !mkdir($target, 0755, true) && !is_dir($target)) $this->handleError('Cannot create image output directory.', $fileName);
                    $path = $target . basename($fileName);
                    $created[] = $path;
                    if ($extension === 'svg') {
                        if (!copy($fileName, $path)) $this->handleError('cannot_copy_image', $fileName);
                    } else {
                        // Always process raster images: small EXIF images must also be oriented.
                        $this->resizeImage($fileName, $path, (int)$dimension);
                    }
                }
            } catch (\Throwable $error) {
                foreach ($created as $path) if (is_file($path)) unlink($path);
                throw $error;
            } finally {
                restore_error_handler();
            }
        }

        /**
         * Retrieves the EXIF orientation value from a JPEG file's metadata.
         * If the file is not a JPEG, EXIF data cannot be read, or the orientation is invalid, a default value is returned.
         *
         * @param string $path The file path of the image to read the EXIF orientation from.
         *
         * @return int The orientation value (1-8) based on the EXIF data, or 1 if not available or invalid.
         */
        public static function getExifOrientation(string $path): int
        {
            $type = @getimagesize($path);
            if (!$type || $type[2] !== IMAGETYPE_JPEG || !function_exists('exif_read_data')) return 1;
            // EXIF is optional. QCubed may render even @-suppressed warnings as HTML,
            // so isolate malformed metadata from its application-wide error handler.
            set_error_handler(static function (): bool { return true; }, E_WARNING | E_NOTICE);
            try {
                $data = exif_read_data($path, 'IFD0', true, false);
            } catch (\Throwable $error) {
                return 1;
            } finally {
                restore_error_handler();
            }
            $orientation = (int)($data['IFD0']['Orientation'] ?? $data['Orientation'] ?? 1);
            return $orientation >= 1 && $orientation <= 8 ? $orientation : 1;
        }

        /**
         * Adjusts the orientation of a given image based on the specified orientation value.
         * The method handles rotation and flipping of the image as required.
         *
         * @param GdImage $image The image resource to be oriented.
         * @param int $orientation The orientation value determining the required transformations.
         *
         * @return GdImage The oriented image resource.
         * @throws \Exception
         */
        protected function orientImage(GdImage $image, int $orientation): GdImage
        {
            $angle = match ($orientation) {3 => 180, 5, 6, 7 => -90, 8 => 90, default => 0};
            if ($angle !== 0) {
                $rotated = imagerotate($image, $angle, 0);
                if ($rotated === false) $this->handleError('cannot_rotate_image');
                $image = $rotated;
            }
            $flip = match ($orientation) {2, 5 => IMG_FLIP_HORIZONTAL, 4, 7 => IMG_FLIP_VERTICAL, default => null};
            if ($flip !== null && !imageflip($image, $flip)) $this->handleError('cannot_mirror_image');
            return $image;
        }

        /**
         * Retrieves the display dimensions of an image, taking its EXIF orientation into account.
         *
         * @param string $path The file path to the image whose dimensions are to be retrieved.
         *
         * @return array|null An array containing the width and height of the image, adjusted for orientation,
         *                    or null if the image dimensions could not be determined.
         */
        public static function getDisplayDimensions(string $path): ?array
        {
            $size = @getimagesize($path);
            if (!$size) return null;
            return self::getExifOrientation($path) >= 5 ? [$size[1], $size[0]] : [$size[0], $size[1]];
        }

        /**
         * Removes files derived from the original file by traversing temporary folders and deleting
         * matching files in the specified storage path.
         *
         * @param string $original The name of the original file whose derived files should be removed.
         *
         * @return void
         */
        protected function removeDerivedFiles(string $original): void
        {
            foreach ($this->options['TempFolders'] as $folder) {
                $path = $this->options['FullStoragePath'] . '/' . $folder . '/';
                if ($this->options['DestinationPath']) $path .= trim($this->options['DestinationPath'], '/') . '/';
                $path .= basename($original);
                if (is_file($path)) unlink($path);
            }
        }

        /**
         * Creates a new GD image resource from a file based on the specified image type.
         *
         * @param string $path The path to the image file to be loaded.
         * @param int $type The type of the image (e.g., IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, etc.).
         *
         * @return GdImage|false Returns a GD image resource on success or false on failure.
         */
        protected function imageCreateFrom(string $path, int $type): GdImage|false
        {
            if (empty($path) || empty($type)) return false;

            return match ($type) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($path),
                IMAGETYPE_PNG  => imagecreatefrompng($path),
                IMAGETYPE_GIF  => imagecreatefromgif($path),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
                IMAGETYPE_BMP  => function_exists('imagecreatefrombmp') ? imagecreatefrombmp($path) : false,
                default        => false,
            };
        }

        /**
         * Applies a sharpening filter to the given image using a convolution matrix.
         *
         * @param resource $image The image resource to be sharpened.
         *
         * @return void
         */
        protected function sharpenImage($image): void
        {
            $matrix = array(
                array(-1, -1, -1),
                array(-1, 20, -1),
                array(-1, -1, -1),
            );
            $divisor = array_sum(array_map('array_sum', $matrix));
            $offset = 0;
            imageconvolution($image, $matrix, $divisor, $offset);
        }

        /**
         * Resizes an image to the specified dimensions and saves the resized image to a new path.
         *
         * @param string $path The full path of the source image to be resized.
         * @param string $newPath The full path where the resized image will be saved.
         * @param int $resizeDimensions The maximum width or height of the resized image.
         *
         * @return void
         * @throws Exception If the image type is unsupported or an error occurs during processing.
         */
        protected function resizeImage(string $path, string $newPath, int $resizeDimensions): void
        {
            $size = @getimagesize($path);
            if (!$size || $size[0] < 1 || $size[1] < 1 || $resizeDimensions < 1) $this->handleError($this->getErrorMessage('invalid_image'), $path);
            $types = ['jpg' => IMAGETYPE_JPEG, 'jpeg' => IMAGETYPE_JPEG, 'png' => IMAGETYPE_PNG,
                'gif' => IMAGETYPE_GIF, 'bmp' => IMAGETYPE_BMP, 'webp' => IMAGETYPE_WEBP];
            if (($types[strtolower(pathinfo($path, PATHINFO_EXTENSION))] ?? null) !== $size[2]) $this->handleError('image_content_does_not_match_its_extension', $path);
            $ratio = min(1, $resizeDimensions / max($size[0], $size[1]));
            $width = max(1, (int)round($size[0] * $ratio));
            $height = max(1, (int)round($size[1] * $ratio));
            $pixels = (float)$size[0] * $size[1];
            if ($this->options['MaxImagePixels'] && $pixels > $this->options['MaxImagePixels']) $this->handleError('image_resolution_exceeds_the_allowed_pixel_count', $path);
            // Conservative estimate, not a guarantee: GD allocation varies by build/format.
            // Rotate the resized result, avoiding a second full-resolution image allocation.
            $estimate = $pixels * 5 + $width * $height * 12 + 8 * 1024 * 1024;
            $limit = self::configBytes(ini_get('memory_limit'));
            if ($limit > 0 && memory_get_usage(true) + $estimate > $limit) $this->handleError('not_enough_server_memory_to_process_this_image_resolution' , $path);
            $orientation = $this->options['AutoOrient'] ? self::getExifOrientation($path) : 1;
            $image = $this->imageCreateFrom($path, $size[2]);
            if ($image === false) $this->handleError($this->getErrorMessage('failed_to_load_image'), $path);
            $output = imagecreatetruecolor($width, $height);
            if ($output === false) $this->handleError($this->getErrorMessage('failed_to_create_image'), $path);
            imagealphablending($output, false);
            imagesavealpha($output, true);
            $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
            imagefill($output, 0, 0, $transparent);
            $resize = $this->options['ImageResizeFunction'];
            if (!in_array($resize, ['imagecopyresampled', 'imagecopyresized'], true) ||
                !$resize($output, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1])) {
                $this->handleError($this->getErrorMessage('failed_to_resize_image'), $path);
            }
            unset($image);
            $output = $this->orientImage($output, $orientation);
            if ($this->options['ImageResizeSharpen']) $this->sharpenImage($output);
            $quality = min(100, max(0, (int)$this->options['ImageResizeQuality']));
            $saved = match ($size[2]) {
                IMAGETYPE_JPEG => imagejpeg($output, $newPath, $quality),
                IMAGETYPE_PNG => imagepng($output, $newPath, min(9, max(0, (int)$this->options['PngLevel']))),
                IMAGETYPE_GIF => imagegif($output, $newPath),
                IMAGETYPE_WEBP => function_exists('imagewebp') && imagewebp($output, $newPath, $quality),
                IMAGETYPE_BMP => function_exists('imagebmp') && imagebmp($output, $newPath),
                default => false,
            };
            clearstatcache(true, $newPath);
            if (!$saved || !is_file($newPath) || filesize($newPath) === 0) $this->handleError('cannot_save_resized_image', $path);
        }

        /**
         * Outputs information about the uploaded file in JSON format.
         *
         * @return void
         */
        protected function uploadInfo(): void
        {
            print json_encode(array(
                'filename' =>  basename($this->options['FileName']),
                'path' => $this->getRelativePath($this->options['FileName']),
                'extension' => $this->getExtension($this->options['FileName']),
                'type' => $this->options['FileType'],
                'error' => $this->options['FileError'],
                'size' => $this->options['FileSize'],
                'mtime' => filemtime($this->options['FileName']),
                'dimensions' => $this->getDimensions($this->options['FileName'])
            ));
        }

        /**
         * Handles errors encountered during the file upload process, outputs an error response in JSON format,
         * and performs cleanup of temporary files if necessary.
         *
         * @param string $errorMessage The error message describing the issue.
         * @param string|null $file The path to the file associated with the error, if available.
         *
         * @return void
         * @throws \Exception
         */
        public function handleError(string $errorMessage, ?string $file = null): void
        {
            $json = array(
                'filename' => $file ? basename($file) : null,
                'size' => $this->options['FileSize'] ?? 0,
                'type' => $this->options['FileType'] ?? null,
                'error' => $errorMessage
            );

            throw new Exception(json_encode($json, JSON_UNESCAPED_UNICODE));
        }

        /**
         * Fixes integer overflow for a given size by adjusting negative values caused by exceeding PHP's integer range.
         *
         * @param float|int $size The value to process, which might have overflowed into a negative integer.
         *
         * @return int|float The corrected size value after resolving the overflow.
         */
        protected function fixIntegerOverflow(float|int $size): float|int
        {
            if ($size < 0) {
                $size += 2.0 * (PHP_INT_MAX + 1);
            }
            return $size;
        }

        /**
         * Retrieves the size of a specified file, with an option to clear the filesystem cache beforehand.
         *
         * @param string $filePath The path to the file whose size is to be determined.
         * @param bool $clearStatCache Whether to clear the filesystem cache before getting the file size. Defaults to false.
         *
         * @return float|false|int The file size in bytes on success, or false on failure.
         */
        protected function getFileSize(string $filePath, ?bool $clearStatCache = false): float|false|int
        {
            if ($clearStatCache) {
                if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
                    clearstatcache(true, $filePath);
                } else {
                    clearstatcache();
                }
            }
            return $this->fixIntegerOverflow(filesize($filePath));
        }

        /**
         * Converts a given value to its corresponding size in bytes.
         * The method supports plain numeric values or values with suffixes 'k', 'm', or 'g'
         * representing kilobytes, megabytes, and gigabytes, respectively.
         *
         * @param mixed $val The input value to be converted. It can be numeric or a string ending with a size suffix.
         *
         * @return int Returns the size in bytes as an integer.
         */
        public function getConfigBytes(mixed $val): int
        {
            return self::configBytes($val);
        }

        /**
         * Removes the file name from the given file path, returning the directory path.
         *
         * @param string $path The full file path from which the file name should be removed.
         *
         * @return string The directory path without the file name.
         */
        protected function removeFileName(string $path): string
        {
            return substr($path, 0, (int) strrpos($path, '/'));
        }

        /**
         * Retrieves the relative path of a file or directory based on the configured root path.
         *
         * @param string $path The absolute path to be converted into a relative path.
         *
         * @return string The relative path derived from the provided absolute path.
         */
        public function getRelativePath(string $path): string
        {
            return substr($path, strlen($this->options['RootPath']));
        }

        /**
         * Retrieves the extension of a given file path.
         *
         * @param string $path The file path from which to extract the extension.
         *
         * @return string|null The file extension in lowercase, or null if the path is not a valid file.
         */
        public static function getExtension(string $path): ?string
        {
            if(!is_dir($path) && is_file($path)){
                return strtolower(substr(strrchr($path, '.'), 1));
            }

            return null;
        }

        /**
         * Determines the MIME type of file at the specified path.
         *
         * @param string $path The file path for which the MIME type needs to be determined.
         *
         * @return string|false Returns the MIME type of the file as a string or false on failure.
         */
        public static function getMimeType(string $path): false|string
        {
            if(function_exists('mime_content_type')) {
                return mime_content_type($path);
            } else {
                return function_exists('finfo_file') ? finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path) : false;
            }
        }

        /**
         * Retrieves the dimensions of an image file in the format "width x height".
         *
         * @param string $path The file path of the image whose dimensions need to be retrieved.
         *
         * @return string|null The dimensions of the image as a string in the format "width x height",
         *                     or null if the file is not an image or dimensions cannot be determined.
         */
        public static function getDimensions(string $path): ?string
        {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($ext === 'svg') {
                // Finding SVG dimensions from XML
                if (!is_file($path)) {
                    return null;
                }
                $svg = @simplexml_load_file($path);
                if ($svg === false) {
                    return null;
                }

                $attributes = $svg->attributes();

                $width = isset($attributes->width) ? (string)$attributes->width : null;
                $height = isset($attributes->height) ? (string)$attributes->height : null;

                // If width/height is missing, take from viewBox
                if (!$width || !$height) {
                    if (isset($attributes->viewBox)) {
                        $viewBox = preg_split('/[\s,]+/', (string)$attributes->viewBox);
                        if (count($viewBox) === 4) {
                            $width  = $viewBox[2];
                            $height = $viewBox[3];
                        }
                    }
                }

                // Remove possible units (e.g. "100px" -> "100")
                if ($width !== null) {
                    $width = preg_replace('/[a-z%]+/i', '', $width);
                }
                if ($height !== null) {
                    $height = preg_replace('/[a-z%]+/i', '', $height);
                }

                if ($width && $height) {
                    return $width . ' x ' . $height;
                }

                return null;
            }

            // Other visual image (e.g. jpg, png, etc.)
            $imageSize = self::getDisplayDimensions($path);

            if ($imageSize && in_array($ext, self::getImageExtensions(), true)) {
                $width = ($imageSize[0] ?? '0');
                $height = ($imageSize[1] ?? '0');
                return $width . ' x ' . $height;
            }

            return null;
        }

        /**
         * Retrieves a list of supported image file extensions.
         *
         * @return array A list of image file extensions as strings.
         */
        public static function getImageExtensions(): array
        {
            return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif', 'svg');
        }

        /**
         * Converts a byte size into a human-readable format with appropriate units.
         *
         * @param float|int $bytes The size in bytes to be converted.
         *
         * @return string The formatted size with its respective unit (e.g., KB, MB, GB).
         */
        protected function readableBytes(float|int $bytes): string
        {
            $i = floor(log($bytes) / log(1024));
            $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
        }

        /**
         * Converts an uploaded filename to portable ASCII while preserving letter case.
         * Spaces, hyphens and other unsupported characters become underscores.
         * The final extension is preserved; no optional PHP extensions are required.
         */
        public static function sanitizeForFile(string $filename): string
        {
            return QString::sanitizeForFile($filename);
        }

        /**
         * Cleans and normalizes a file path by trimming unwanted characters, removing unsafe sequences,
         * and standardizing directory separators.
         *
         * @param string $path The file path to be cleaned and normalized.
         *
         * @return string The cleaned and normalized file path.
         */
        public static function cleanPath(string $path): string
        {
            $path = trim($path);
            $path = trim($path, '\\/');
            $path = str_replace(array('../', '..\\'), '', $path);
            if ($path == '..') {
                $path = '';
            }
            return str_replace('\\', '/', $path);
        }

        /**
         * Renames a file or directory, moving it from the old path to the new path.
         *
         * @param string $old The current path of the file or directory to be renamed.
         * @param string $new The new path for the file or directory.
         *
         * @return bool|null Returns true if the rename was successful, false if it failed, or null if the conditions were not met.
         */
        protected function rename(string $old, string $new): ?bool
        {
            return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
        }
    }