<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Control\FormBase;
use QCubed\Control\ControlBase;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\Folder;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class Filemanager
 *
 * Note: the "upload" folder must already exist in /project/assets/ and this folder has 777 permissions.
 *
 * @property string $RootPath Default root path APP_UPLOADS_DIR. You may change the location of the file repository
 *                             at your own risk.
 * @property string $RootUrl Default root url APP_UPLOADS_URL. If necessary, the root url must be specified.
 * @property string $TempPath = Default temp path APP_UPLOADS_TEMP_DIR. If necessary, the temp dir must be specified.
 * @property string $TempUrl Default temp url APP_UPLOADS_TEMP_URL. If necessary, the temp url must be specified.
 * @property string $DateTimeFormat Default date() format is blank, set the appropriate format.
 *                                  PHP date() format for file modification date.
 * @property string $SelectedItems
 * @property string $StoragePath Default dir named _files. This dir is generated together with the dirs
 *                               /thumbnail,  /medium,  /large, /zip when the corresponding page is opened for the first time.
 * @property string $FullStoragePath Please see the setup() function! Can only be changed in this function.
 *
 * @package QCubed\Plugin
 */

class FileManagerBase extends FileManagerBaseGen
{
    use Q\Control\DataBinderTrait;

    /** @var array */
    protected $arrSelectedItems = null;
    /** @var string */
    protected $strRootPath = APP_UPLOADS_DIR;
    /** @var string */
    protected $strRootUrl = APP_UPLOADS_URL;
    /** @var string */
    protected $strTempPath = APP_UPLOADS_TEMP_DIR;
    /** @var string */
    protected $strTempUrl = APP_UPLOADS_TEMP_URL;
    /** @var string */
    protected $strStoragePath = '_files';
    /** @var string */
    protected $strFullStoragePath;

    /**
     * @param $objParentObject
     * @param $strControlId
     * @throws Caller
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->registerFiles();
        $this->setup();;
    }

  /**
   * @throws Caller
   */
    protected function registerFiles() {
        $this->AddJavascriptFile("https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.9/dayjs.min.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.filemanager.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.uploadhandler.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/jquery.slimscroll.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/custom.js");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.filemanager.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.uploadhandler.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/custom.css");
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
    }

    /**
     * @return void
     * @throws Caller
     */
    protected function setup()
    {
        $this->strFullStoragePath = $this->strTempPath . '/' . $this->strStoragePath;
        $strCreateDirs = ['/thumbnail', '/medium', '/large', '/zip'];

        if (!is_dir($this->strRootPath)) {
            Folder::makeDirectory(QCUBED_PROJECT_DIR . '/assets/upload', 0777);
        }

        if (!is_dir($this->strFullStoragePath)) {
            Folder::makeDirectory($this->strFullStoragePath, 0777);
            foreach ($strCreateDirs as $strCreateDir) {
                Folder::makeDirectory($this->strFullStoragePath . $strCreateDir, 0777);
            }
        }

        if($_SERVER['REQUEST_METHOD'] == "POST") {exit;} // prevent loading entire page in the echo

        $isHttps = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';

        /** clean and check $strRootPath */
        $this->strRootPath = rtrim($this->strRootPath, '\\/');
        $this->strRootPath = str_replace('\\', '/', $this->strRootPath);

        $permissions = fileperms($this->strRootPath);
        $permissions = substr(sprintf('%o', $permissions), -4);
        if (!Folder::isWritable($this->strRootPath)) {
            throw new Caller('Root path "' . $this->strRootPath . '" not writable or not found and
            it has the following directory permissions: ' . $permissions . '. Please set 0755 or 0777 permissions to the
            directory or create a directory "upload" into the location "/project/assets" and grant permissions 0755 or 0777!');
        };

        if (!Folder::isWritable($this->strRootPath)) {
            throw new Caller('Root path "' . $this->strRootPath . '" not writable or not found and
            it has the following directory permissions: ' . $permissions . '. Please set 0755 or 0777 permissions to the
            directory or create a directory "upload" into the location "/project/assets" and grant permissions 0755 or 0777!');
        };

        if (!Folder::isWritable($this->strFullStoragePath) && isset($this->strFullStoragePath)) {
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
     * Clean path
     * @param string $path
     * @return string
     */
    protected function cleanPath($path)
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
     * Returns the HTML for the control.
     *
     * @return string
     */
    public function getControlHtml()
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

    protected static function readableBytes($bytes)
    {
        $i = floor(log($bytes) / log(1024));
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
    }

    public function rename($old, $new)
    {
        return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
    }

    /**
     * Get file path without filename
     * @param $path
     * @return string
     */
    public function removeFileName($path)
    {
        return substr($path, 0, (int) strrpos($path, '/'));
    }

    /**
     * Get file path without RootPath
     * @param $path
     * @return string
     */
    public function getRelativePath($path)
    {
        return substr($path, strlen($this->strRootPath));
    }

    /**
     * Get file extension
     * @param string $path
     * @return mixed|string
     */
    public static function getExtension($path)
    {
        if(!is_dir($path) && is_file($path)){
            return strtolower(substr(strrchr($path, '.'), 1));
        }
    }

    /**
     * Get mime type
     * @param string $path
     * @return mixed|string
     */
    public static function getMimeType($path)
    {
        if(function_exists('mime_content_type')){
            return mime_content_type($path);
        } else {
            return function_exists('finfo_file') ? finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path) : false;
        }
    }

    /**
     * Get size of an image
     * @param string $path
     * @return mixed|string
     */
    public static function getDimensions($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions()))
        {
            $width = (isset($ImageSize[0]) ? $ImageSize[0] : '0');
            $height = (isset($ImageSize[1]) ? $ImageSize[1] : '0');
            $dimensions = $width . ' x ' . $height;
            return $dimensions;
        }
    }

    /**
     * Get image files extensions
     * @return array
     */
    public static function getImageExtensions()
    {
        return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
    }

    /**
     * Generated method overrides the built-in Control method, causing it to not redraw completely. We restore
     * its functionality here.
     */
    public function refresh()
    {
        parent::refresh();
        ControlBase::refresh();
    }

    protected function makeJqWidget()
    {
        $strJS = parent::makeJqWidget();

        $strCtrlJs = <<<FUNC
jQuery('#{$this->ControlId}').on("selectablestop", function (event, ui) {
    const items = jQuery('#{$this->ControlId}').find(".ui-selected");
    const result = [];
    for (var i = 0, len = items.length; i < len; i++) {
        const item = items[i],
            itemDetails = {
                "data-id": item.getAttribute("data-id"),
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
        Application::executeJavaScript($strCtrlJs, Application::PRIORITY_HIGH);

        return $strJS;
    }

    public function getEndScript()
    {
        $strJS = parent::getEndScript();

        $strCtrlJs = <<<FUNC
jQuery('#{$this->ControlId}').selectable({filter:'[data-type="media-item"]', autoRefresh: true})
FUNC;
        Application::executeJavaScript($strCtrlJs, Application::PRIORITY_HIGH);

        return $strJS;
    }

    /**
     * @param $strName
     * @return array|bool|callable|float|int|mixed|string|null
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'SelectedItems': return $this->arrSelectedItems;
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
     * @param $strName
     * @param $mixValue
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
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
