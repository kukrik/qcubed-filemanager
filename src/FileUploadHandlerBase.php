<?php

    namespace QCubed\Plugin;

    use QCubed\Control\ControlBase;
    use QCubed\Control\FormBase;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Folder;
    use QCubed\Type;

    /**
     * Class FileUploadHandler
     *
     * Note: the "upload" folder must already exist in /project/assets/ and this folder has 777 permissions.
     *
     * @property string $RootPath Default root path APP_UPLOADS_DIR. You may change the location of the file repository
     *                             at your own risk.
     * @property string $RootUrl Default root url APP_UPLOADS_URL. If necessary, the root url must be specified.
     *
     * Note: If you want to change TempPath, TempUrl and StoragePath, you have to rewrite the setup() function in the FileUpload class.
     * This class is located in the /project/includes/plugins folder.
     *
     * @property string $TempPath = Default temp path APP_UPLOADS_TEMP_DIR. If necessary, the temp dir must be specified.
     * @property string $TempUrl Default temp url APP_UPLOADS_TEMP_URL. If necessary, the temp url must be specified.
     * @property string $StoragePath Default dir named _files. This dir is generated together with the dirs
     *                               /thumbnail, /medium, /large when the corresponding page is opened for the first time.
     * @property string $FullStoragePath Please a see the setup() function! Can only be changed in this function.
     *
     * @package QCubed\Plugin
     */

    class FileUploadHandlerBase extends FileUploadHandlerBaseGen
    {
        /** @var string[] */
        protected array $strFormAttributes = array('enctype' => 'multipart/form-data');
        /** @var string */
        protected string $strRootPath = APP_UPLOADS_DIR;
        /** @var string */
        protected string $strRootUrl = APP_UPLOADS_URL;
        /** @var string */
        protected string $strTempPath = APP_UPLOADS_TEMP_DIR;
        /** @var string */
        protected string $strTempUrl = APP_UPLOADS_TEMP_URL;
        /** @var string */
        protected string $strStoragePath = '_files';
        /** @var string */
        protected string $strFullStoragePath;

        /**
         * Constructor for the class initializes the object with a parent and an optional control ID.
         *
         * @param ControlBase|FormBase $objParentObject The parent object of the control, which must be an instance of ControlBase or FormBase.
         * @param string|null $strControlId An optional identifier for the control. If not provided, a default will be generated.
         *
         * @throws Caller
         */
        public function  __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);

            $this->registerFiles();
            $this->setup();
        }

        /**
         * Registers the necessary JavaScript and CSS files for the file upload functionality.
         *
         * This method adds the required JavaScript and CSS files related to file upload and ensures
         * that the bootstrap CSS file is also included for consistent styling.
         *
         * @return void
         * @throws Caller
         */
        protected function registerFiles(): void
        {
            $this->AddJavascriptFile(QCUBED_FILEUPLOAD_HANDLER_ASSETS_URL . "/js/qcubed.uploadhandler.js");
            $this->addCssFile(QCUBED_FILEUPLOAD_HANDLER_ASSETS_URL . "/css/qcubed.uploadhandler.css");
            $this->addCssFile(QCUBED_FILEUPLOAD_HANDLER_ASSETS_URL . "/css/custom-svg-icons.css");
            $this->addCssFile(QCUBED_FILEUPLOAD_HANDLER_ASSETS_URL . "/css/vauu-table.css");
            $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        }

        /**
         * Generates and returns the HTML for the control.
         *
         * This method is responsible for creating the HTML structure
         * of the control, specifically a div element with the class "files".
         * It formats and returns the generated HTML as a string.
         *
         * @return string The HTML content of the control.
         */
        protected function getControlHtml(): string
        {
            return _nl('<div class="files"></div>');
        }

        /**
         * Sets up the necessary directories, validates paths and permissions, and configures root and temporary URLs.
         *
         * This method ensures the directory structure for storage and temporary files is in place.
         * It validates paths, checks permissions for writability, and constructs proper URLs for the root
         * and temporary paths based on the server environment and protocol.
         *
         * It verifies that the required directories exist, such as storage subdirectories like
         * "thumbnail", "medium", and "large". If they do not exist, the method creates them with proper permissions.
         * The method also validates whether the server root and storage directories are writable, throwing an exception if they are not.
         * Paths are sanitized and URLs are constructed based on the HTTPS protocol or HTTP host.
         *
         * @return void
         * @throws Caller
         */
        protected function setup(): void
        {
            $this->strFullStoragePath = $this->strTempPath . '/' . $this->strStoragePath;
            $strCreateDirs = ['/thumbnail', '/medium', '/large', '/zip', '/temp'];

            if (!is_dir($this->strRootPath)) {
                Folder::makeDirectory(QCUBED_PROJECT_DIR . '/assets/upload', 0777);
            }

            if (!is_dir($this->strFullStoragePath)) {
                Folder::makeDirectory($this->strFullStoragePath, 0777);
                foreach ($strCreateDirs as $strCreateDir) {
                    Folder::makeDirectory($this->strFullStoragePath . $strCreateDir, 0777);
                }
            }

            if($_SERVER['REQUEST_METHOD'] == "POST") {exit;} // prevent loading the entire page in the echo

            $isHttps = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
                || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';

            /** Clean and check $strRootPath */
            $this->strRootPath = rtrim($this->strRootPath, '\\/');
            $this->strRootPath = str_replace('\\', '/', $this->strRootPath);

            $permissions = fileperms($this->strRootPath);
            $permissions = substr(sprintf('%o', $permissions), -4);
            if (!Folder::isWritable($this->strRootPath)) {
                throw new Caller('Root path "' . $this->strRootPath . '" not writable or not found, and
            it has the following directory permissions: ' . $permissions . '. Please set 0755 or 0777 permissions to the
            directory or create a directory "upload" into the location "/project/assets" and grant permissions 0755 or 0777!');
            }

            if (!Folder::isWritable($this->strRootPath)) {
                throw new Caller('Root path "' . $this->strRootPath . '" not writable or not found, and
            it has the following directory permissions: ' . $permissions . '. Please set 0755 or 0777 permissions to the
            directory or create a directory "upload" into the location "/project/assets" and grant permissions 0755 or 0777!');
            }

            if (!Folder::isWritable($this->strFullStoragePath)) {
                throw new Caller('Storage path "' . $this->strTempPath . '/' . $this->strStoragePath .
                    '" not writable or not found." Please set permissions to the 0777 directory "/project/tmp", the "_files" folder and subfolders!');
            }

            clearstatcache();
            /** clean $strRootUrl */
            $this->strRootUrl = $this->cleanPath($this->strRootUrl);
            /** clean $strTempUrl */
            $this->strTempUrl = $this->cleanPath($this->strTempUrl);
            /** Server hostname. Can set manually if wrong. Don't change! */
            $strHttpHost = $_SERVER['HTTP_HOST'];

            $this->strRootUrl = $isHttps ? 'https' : 'http' . '://' . $strHttpHost . (!empty($this->strRootUrl) ? '/' . $this->strRootUrl : '');
            $this->strTempUrl = $isHttps ? 'https' : 'http' . '://' . $strHttpHost . (!empty($this->strTempUrl) ? '/' . $this->strTempUrl : '');
        }

        /**
         * Cleans and normalizes a given file path by removing unnecessary characters and sanitizing it.
         *
         * This method trims leading and trailing spaces, removes slashes and backslashes
         * from the start and end, and prevents directory traversal by stripping occurrences
         * of "../" or "..\". It also converts all backslashes to forward slashes for consistency.
         * If the resulting path is equivalent to "..", it returns an empty string.
         *
         * @param string $path The file path to be cleaned and sanitized.
         *
         * @return string The sanitized and normalized file path.
         */
        protected function cleanPath(string $path): string
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
         * Magic method to retrieve property values dynamically.
         *
         * This method provides access to specific protected properties such as
         * "RootPath", "RootUrl", "TempPath", "TempUrl", and "StoragePath". If the requested
         * property does not match any predefined cases, the method attempts to retrieve it
         * through the parent::__get mechanism. If the property is still unavailable, a Caller
         * exception is thrown.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the requested property does not exist or is inaccessible.
         */
        public function __get($strName): mixed
        {
            switch ($strName) {
                case "RootPath": return $this->strRootPath;
                case "RootUrl": return $this->strRootUrl;
                case "TempPath": return $this->strTempPath;
                case "TempUrl": return $this->strTempUrl;
                case "StoragePath": return $this->strStoragePath;

                default:
                    try {
                        return parent::__get($strName);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }

        /**
         * Magic method for setting the values of class properties dynamically.
         *
         * This method assigns values to various properties, such as `RootPath`, `RootUrl`, `TempPath`, `StoragePath`,
         * and `FullStoragePath`, after validating and casting the provided values to the appropriate types. If invalid
         * casting is attempted, it throws an exception. It also marks the object as modified when a property is changed.
         * For unrecognized property names, the method attempts to set the value on the parent class and handles any
         * exceptions that may arise.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the specified property.
         *
         * @return void
         * @throws InvalidCast If the provided value cannot be cast to the required type.
         * @throws Caller If the parent class cannot handle the property or value assignment.
         */
        public function __set($strName, mixed $mixValue): void
        {
            switch ($strName) {
                case "RootPath":
                    try {
                        $this->strRootPath = Type::Cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                case "RootUrl":
                    try {
                        $this->strRootUrl = Type::Cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                case "TempPath":
                    try {
                        $this->strTempPath = Type::Cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                case "StoragePath":
                    try {
                        $this->strStoragePath = Type::Cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                case "FullStoragePath":
                    try {
                        $this->strFullStoragePath = Type::Cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                default:
                    try {
                        parent::__set($strName, $mixValue);
                        break;
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }
    }
