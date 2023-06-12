<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Control\FormBase;
use QCubed\Control\ControlBase;
use QCubed\Folder;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
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
 *
 * @property string $TempPath = Default temp path APP_UPLOADS_TEMP_DIR. If necessary, the temp dir must be specified.
 * @property string $TempUrl Default temp url APP_UPLOADS_TEMP_URL. If necessary, the temp url must be specified.
 *
 * @property string $CurrentPath
 *
 * @property object $DataSource
 * @property object $FolderSource
 * @property object $FileSource
 *
 * @package QCubed\Plugin
 */

class FileManagerBase extends FileManagerBaseGen
{
    use Q\Control\DataBinderTrait;

    /** @var string */
    protected $strRootPath = APP_UPLOADS_DIR;
    /** @var string */
    protected $strRootUrl = APP_UPLOADS_URL;
    /** @var string */
    protected $strTempPath = APP_UPLOADS_TEMP_DIR;
    /** @var string */
    protected $strTempUrl = APP_UPLOADS_TEMP_URL;
    /** @var string */
    protected $strCurrentPath = '';
    /** @var string */
    protected $strStoragePath = '_files';
    /** @var string */
    protected $strFullStoragePath;
    /** @var  callable */
    protected $folderParamsCallback = null;
    /** @var  callable */
    protected $fileParamsCallback = null;
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;
    protected $objVariables = [];
    protected $arrFolders = [];
    protected $arrFiles = [];



    public function  __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller  $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
        $this->registerFiles();
        // $this->setup();
    }

    /**
     * @throws Caller
     */
    protected function registerFiles() {
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.filemanager.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.uploadhandler.js");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.filemanager.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.uploadhandler.css");
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
    }

    public function createRenderFolders(callable $callback)
    {
        $this->folderParamsCallback = $callback;
    }

    public function getFolderParam($objItem)
    {
        if (!$this->folderParamsCallback) {
            throw new \Exception("Must provide an folderParamsCallback");
        }
        $params = call_user_func($this->folderParamsCallback, $objItem);

        $intId = '';
        if (isset($params['id'])) {
            $intId = $params['id'];
        }
        $strParentId = '';
        if (isset($params['parent_id'])) {
            $strParentId = $params['parent_id'];
        }
        $strName = '';
        if (isset($params['name'])) {
            $strName = $params['name'];
        }
        $strType = '';
        if (isset($params['type'])) {
            $strType = $params['type'];
        }
        $strPath = '';
        if (isset($params['path'])) {
            $strPath = $params['path'];
        }
        $ctlCreatedDate = '';
        if (isset($params['created_date'])) {
            $ctlCreatedDate = $params['created_date'];
        }
        $intMTime = '';
        if (isset($params['mtime'])) {
            $intMTime = $params['mtime'];
        }
        $intLockedFile = '';
        if (isset($params['locked_file'])) {
            $intLockedFile = $params['locked_file'];
        }

        $vars = [
            'id' => $intId,
            'parent_id' => $strParentId,
            'name' => $strName,
            'type' => $strType,
            'path' => $strPath,
            'created_date' => $ctlCreatedDate,
            'mtime' => $intMTime,
            'locked_file' => $intLockedFile,
            'items' => ''
        ];
        return $vars;
    }

    public function getFileParam($objItem)
    {
        $vars = [
            'id' => $objItem->getId(),
            'folder_id' => $objItem->getFolderId(),
            'name' => $objItem->getName(),
            'type' => $objItem->getType(),
            'path' => $objItem->getPath(),
            'description' => $objItem->getDescription(),
            'extension' => $objItem->getExtension(),
            'mime_type' => $objItem->getMimeType(),
            'size' => $objItem->getSize(),
            'mtime' => $objItem->getMTime(),
            'dimensions' => $objItem->getDimensions(),
            'locked_file' => $objItem->getLockedFile(),
        ];
        return $vars;
    }

    /**
     * Fix up possible embedded reference to the form.
     */
    public function sleep()
    {
        $this->folderParamsCallback= Q\Project\Control\ControlBase::sleepHelper($this->folderParamsCallback);
        parent::sleep();
    }

    /**
     * The object has been unserialized, so fix up pointers to embedded objects.
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->folderParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->folderParamsCallback);
    }

    /**
     * Returns the HTML for the control.
     *
     * @return string
     */
    protected function getControlHtml()
    {
        $this->dataBind();

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                if ($objObject->Id == 1 || $objObject->ParentId == 1) {
                    $this->arrFolders[] = $this->getFolderParam($objObject);
                }

                foreach ($objObject->FilesAsFolderArray as $objFile) {
                    if ($objObject->Id == $objFile->FolderId) {
                        $this->arrFiles[] = $this->getFileParam($objFile);
                    }
                }
                $this->objVariables = array_merge($this->arrFolders, $this->arrFiles);
            }
        }

        //header('Content-type: application/json');
        print '<pre>';
        //print json_encode($this->objVariables, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR);


        //print_r($this->objDataSource);
        //print_r($this->objVariables);
        //print_r($this->arrFolders);
        //print_r($this->arrFiles);
        print '</pre>';
        $this->objDataSource = null;
    }

    /**
     * @throws Caller
     */
    public function dataBind()
    {
        // Run the DataBinder (if applicable)
        if (($this->objDataSource === null) && ($this->hasDataBinder()) && (!$this->blnRendered)) {
            try {
                $this->callDataBinder();
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }
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
     * Generated method overrides the built-in Control method, causing it to not redraw completely. We restore
     * its functionality here.
     */
    public function refresh()
    {
        parent::refresh();
        ControlBase::refresh();
    }



    public function getEndScript()
    {
        $output = json_encode($this->objVariables, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR);
        Application::executeJavaScript(sprintf("var _c = {$output}; //console.log(Object.assign({}, _c));"));
    }

    /**
     * @param $strName
     * @return array|bool|callable|float|int|mixed|string|null
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "RootPath": return $this->strRootPath;
            case "RootUrl": return $this->strRootUrl;
            case "TempPath": return $this->strTempPath;
            case "TempUrl": return $this->strTempUrl;
            case "CurrentPath": return $this->strCurrentPath;
            case "StoragePath": return $this->strStoragePath;
            case "DataSource": return $this->objDataSource;

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
            case "CurrentPath":
                try {
                    $this->strCurrentPath = Type::Cast($mixValue, Type::STRING);
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
            case "DataSource":
                $this->blnModified = true;
                $this->objDataSource = $mixValue;
                break;

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
