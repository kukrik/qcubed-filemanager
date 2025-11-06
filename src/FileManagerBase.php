<?php

    namespace QCubed\Plugin;

    use QCubed as Q;
    use QCubed\ApplicationBase;
    use QCubed\Bootstrap\Bootstrap;
    use QCubed\Project\Control\FormBase;
    use QCubed\Project\Control\ControlBase;
    use QCubed\Exception\InvalidCast;
    use QCubed\Exception\Caller;
    use QCubed\Folder;
    use QCubed\Project\Application;
    use QCubed\Type;

    /**
     * Class FileManager
     *
     * Note: the "upload" folder must already exist in /project/assets/ and this folder has 777 permissions.
     *
     * @property string $RootPath Default root path APP_UPLOADS_DIR. You may change the location of the file repository
     *                             at your own risk.
     * @property string $Path Default path '../assets'. If necessary, the path must be specified.
     * @property string $RootUrl Default root url APP_UPLOADS_URL. If necessary, the root url must be specified.
     * @property string $TempPath = Default temp path APP_UPLOADS_TEMP_DIR. If necessary, the temp dir must be specified.
     * @property string $TempUrl Default temp url APP_UPLOADS_TEMP_URL. If necessary, the temp url must be specified.
     * @property string $DateTimeFormat The default date () format is blank, set the appropriate format.
     *                                  PHP date() format for file modification date.
     * @property string $SelectedItems
     * @property string $StoragePath Default dir named _files. This dir is generated together with the dirs
     *                               /thumbnail, /medium, /large, /temp, /zip when the corresponding page is opened for the first time.
     * @property string $FullStoragePath Please a see the setup() function! Can only be changed in this function.
     *
     * @package QCubed\Plugin
     */

    class FileManagerBase extends FileManagerBaseGen
    {
        use Q\Control\DataBinderTrait;

        /** @var string */
        protected string $arrSelectedItems;

        /** @var string */
        protected string $strPath = '../assets';

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
         * Constructor for initializing the control object.
         *
         * @param ControlBase|FormBase $objParentObject Parent object of the control, either a control or form.
         * @param string|null $strControlId Optional control ID for identifying the control.
         *
         * @throws Caller
         */
        public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);

            $this->registerFiles();
            $this->setup();
        }

        /**
         * Register JavaScript and CSS files required for the file manager functionality.
         *
         * @return void
         * @throws Caller
         */
        protected function registerFiles(): void
        {
            $this->AddJavascriptFile("https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.9/dayjs.min.js");
            $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.filemanager.js");
            $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.uploadhandler.js");
            $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/jquery.slimscroll.js");
            $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL. "/js/custom.js");
            $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/popup-croppie.js");
            $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/exif.js");
            $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/select2.js");
            Bootstrap::loadJS($this);

            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.filemanager.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.fileinfo.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/jquery.fileupload.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/jquery.fileupload-ui.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.uploadhandler.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/custom-buttons-inputs.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/font-awesome.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/custom-switch.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2-bootstrap.css");
            $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2-web-vauu.css");
            $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        }

        /**
         * Set up the storage paths, validate directories, handle permissions,
         * and configure root and temporary URLs.
         *
         * This method initializes and organizes storage paths and directories
         * for the application's upload system. It ensures directories are created
         * with the appropriate permissions, validates writability, and sets up
         * root and temp URLs based on server configuration.
         *
         * @return void
         * @throws Caller
         */
        protected function setup(): void
        {
            $this->strFullStoragePath = $this->strTempPath . '/' . $this->strStoragePath;
            $strCreateDirs = ['/thumbnail', '/medium', '/large', '/temp', '/zip'];

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
         * Cleans and normalizes a filesystem path by removing unnecessary characters and sequences.
         *
         * @param string $path The initial filesystem path to be cleaned.
         *
         * @return string The cleaned and normalized path.
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
         * Generate and return the HTML output for the control.
         *
         * @return string The HTML string representation of the control.
         */
        public function getControlHtml(): string
        {
            $strHtml = '';
            $strHtml .= _nl('<div id="' . $this->ControlId . '">');
            $strHtml .= _nl(_indent('<div class="empty hidden" data-lang="empty_lang">Folder is empty</div>', 1));
            $strHtml .= _nl(_indent('<div class="no-results hidden" data-lang="no_results_lang">No results found</div>', 1));
            $strHtml .= _nl(_indent('<div class="media-items imageList-layout"></div>', 1));
            $strHtml .= _nl(_indent('<div class="media-items list-layout"></div>', 1));
            $strHtml .= _nl(_indent('<div class="media-items box-layout"></div>', 1));
            $strHtml .= '</div>';
            return $strHtml;
        }

        /**
         * Converts a size in bytes to a human-readable format.
         *
         * @param int $bytes The size in bytes to be converted.
         *
         * @return string The human-readable representation of the size (e.g., KB, MB, GB).
         */
        protected static function readableBytes(int $bytes): string
        {
            $i = floor(log($bytes) / log(1024));
            $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
        }

        /**
         * Rename a file or directory if the new name does not already exist and the old name exists
         *
         * @param string $old The current name of the file or directory
         * @param string $new The new name for the file or directory
         *
         * @return bool|null Returns true if the rename was successful, false if it failed, or null if the conditions are not met
         */
        public function rename(string $old, string $new): ?bool
        {
            return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
        }

        /**
         * Remove the file name from a given file path, returning the directory path.
         *
         * @param string $path The complete file path from which the file name will be removed.
         *
         * @return string The directory path without the file name.
         */
        public function removeFileName(string $path): string
        {
            return substr($path, 0, (int) strrpos($path, '/'));
        }

        /**
         * Generate the relative path from the given full path by removing the root path.
         *
         * @param string $path The full file path to the process.
         *
         * @return string The relative path obtained by removing the root path or an empty string if the provided path is empty.
         */
        public function getRelativePath(string $path): string
        {
            if (empty($path)) {
                return ''; // We avoid the error and return an empty string
            }

            return substr($path, strlen($this->strRootPath));
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
         * Determines the MIME type of given file.
         *
         * @param string $path The file path for which to determine the MIME type.
         *
         * @return string|false The MIME type of the file, or false if it cannot be determined.
         */
        public static function getMimeType(string $path): false|string
        {
            if(function_exists('mime_content_type')){
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
            $imageSize = getimagesize($path);

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
         * Refresh the current instance by invoking parent and base component refresh methods.
         * @return void
         */
        public function refresh(): void
        {
            parent::refresh();
            ControlBase::refresh();
        }

        /**
         * Generate and attach jQuery widget JavaScript functionality for the control.
         * The method enhances functionality by including an event handler for the "selectablestop" event.
         * It gathers selected items' data attributes, formats the information, and records control modifications.
         *
         * @return void JavaScript string inherited from the parent implementation.
         * @throws Caller
         */
        protected function makeJqWidget(): void
        {
            $strJS = parent::makeJqWidget();

            $strCtrlJs = <<<FUNC
jQuery('#$this->ControlId').on("selectablestop", function (event, ui) {
    const items = jQuery('#$this->ControlId').find(".ui-selected");
    const result = [];
    for (var i = 0, len = items.length; i < len; i++) {
        const item = items[i],
            itemDetails = {
                "data-id": item.getAttribute("data-id"),
                "data-parent-id": item.getAttribute("data-parent-id"),
                "data-name": item.getAttribute("data-name"),
                "data-type": item.getAttribute("data-type"),
                "data-item-type": item.getAttribute("data-item-type"),
                "data-path": item.getAttribute("data-path"),
                "data-extension": item.getAttribute("data-extension"),
                "data-mimetype": item.getAttribute("data-mime-type"),
                "data-dimensions": item.getAttribute("data-dimensions"),
                "data-size": item.getAttribute("data-size"),
                "data-date": item.getAttribute("data-date"),
                "data-dimensions": item.getAttribute("data-dimensions"),
                "data-locked": item.getAttribute("data-locked"),
                "data-activities-locked": item.getAttribute("data-activities-locked")
        };
        result.push(itemDetails);
    }
    
    qcubed.getFileInfo(result);
    const str = JSON.stringify(result);
    console.log(str);
    qcubed.recordControlModification("$this->ControlId", "_SelectedItems", str);
})
FUNC;
            Application::executeJavaScript($strCtrlJs, ApplicationBase::PRIORITY_HIGH);

            //return $strJS;
        }

        /**
         * Generate and return the end script for the control, including custom JavaScript logic
         * for making elements selectable.
         *
         * @return string
         * @throws Caller
         */
        public function getEndScript(): string
        {
            $strJS = parent::getEndScript();

            $strCtrlJs = <<<FUNC
jQuery('#$this->ControlId').selectable({filter:'[data-type="media-item"]', autoRefresh: true})
FUNC;
            Application::executeJavaScript($strCtrlJs, ApplicationBase::PRIORITY_HIGH);

            return $strJS;
        }

        /**
         * Magic getter method to dynamically access properties by name.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property or the result of the parent's __get method.
         * @throws Caller
         */
        public function __get($strName): mixed
        {
            switch ($strName) {
                case 'SelectedItems': return $this->arrSelectedItems;
                case "Path": return $this->strPath;
                case "RootPath": return $this->strRootPath;
                case "RootUrl": return $this->strRootUrl;
                case "TempPath": return $this->strTempPath;
                case "TempUrl": return $this->strTempUrl;
                case "StoragePath": return $this->strStoragePath;
                case "DateTimeFormat": return $this->strDateTimeFormat;

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
         * Magic method to set the value of a property dynamically.
         * Updates specific internal properties and validates the type of the value.
         *
         * @param string $strName The name of the property being set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws InvalidCast If the value cannot be cast to the required type.
         * @throws Caller If the property does not exist or is inaccessible.
         */
        public function __set($strName, mixed $mixValue): void
        {
            switch ($strName) {
                case '_SelectedItems': // Internal only. Do not use. Used by JS above to track selections.
                    try {
                        $data = Type::cast($mixValue, Type::STRING);
                        $this->arrSelectedItems = $data;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;
                case "Path":
                    try {
                        $this->strPath = Type::Cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
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
                case "TempUrl":
                    try {
                        $this->strTempUrl = Type::Cast($mixValue, Type::STRING);
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
                case "DateTimeFormat":
                    try {
                        $this->strDateTimeFormat = Type::Cast($mixValue, Type::STRING);
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
