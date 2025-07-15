<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Type;

/**
 * Class FileManagerBaseGen
 *
 * @see FileManagerBase
 * @package QCubed\Plugin
 */

/**
 * @property string $Language Default: empty. Optional language selection, default is set to English (en).
 * @property string $RootPath
 * @property string $RootUrl
 * @property string $TempPath
 * @property string $TempUrl
 * @property string $DateTimeFormat
 * @property boolean $IsImageListView
 * @property boolean $IsListView
 * @property boolean $IsBoxView
 * @property string $UpdatedHash
 * @property boolean $LockedDocuments
 * @property boolean $LockedImages
 *
 * @package QCubed\Plugin
 */

class FileManagerBaseGen extends Q\Control\Panel
{
    protected string $strLanguage = "";
    protected string $strRootPath = "";
    protected string $strRootUrl = "";
    protected string $strTempPath = "";
    protected string $strTempUrl = "";
    protected string $strDateTimeFormat = "";
    protected ?bool $blnIsImageListView = null;
    protected ?bool $blnIsListView = null;
    protected ?bool $blnIsBoxView = null;
    protected ?string $strUpdatedHash = null;
    protected ?bool $blnLockedDocuments = null;
    protected ?bool $blnLockedImages = null;

    /**
     * Generates and returns an array of options by gathering properties from the class
     * and combining them with the options from the parent method.
     *
     * Properties are only added to the option array if they are not null.
     *
     * @return array The array of options with the corresponding property values.
     */
    protected function makeJqOptions(): array
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Language)) {$jqOptions['language'] = $val;}
        if (!is_null($val = $this->RootPath)) {$jqOptions['rootPath'] = $val;}
        if (!is_null($val = $this->RootUrl)) {$jqOptions['rootUrl'] = $val;}
        if (!is_null($val = $this->TempPath)) {$jqOptions['tempPath'] = $val;}
        if (!is_null($val = $this->TempUrl)) {$jqOptions['tempUrl'] = $val;}
        if (!is_null($val = $this->DateTimeFormat)) {$jqOptions['dateTimeFormat'] = $val;}
        if (!is_null($val = $this->IsImageListView)) {$jqOptions['isImageListView'] = $val;}
        if (!is_null($val = $this->IsListView)) {$jqOptions['isListView'] = $val;}
        if (!is_null($val = $this->IsBoxView)) {$jqOptions['isBoxView'] = $val;}
        if (!is_null($val = $this->UpdatedHash)) {$jqOptions['updatedHash'] = $val;}
        if (!is_null($val = $this->LockedDocuments)) {$jqOptions['lockedDocuments'] = $val;}
        if (!is_null($val = $this->LockedImages)) {$jqOptions['lockedImages'] = $val;}
        return $jqOptions;
    }

    /**
     * Returns the name of the jQuery setup function to be used for initialization.
     *
     * @return string The name of the jQuery setup function.
     */
    protected function getJqSetupFunction(): string
    {
        return 'fileManager';
    }

    /**
     * Retrieves the value of a specified property dynamically.
     *
     * This method checks for the requested property and returns its value if defined.
     * If the property is not recognized, it delegates the request to the parent::__get method.
     *
     * @param string $strName The name of the property to retrieve.
     *
     * @return mixed The value of the requested property if it exists, otherwise the result of the parent::__get method.
     * @throws Caller If the property is not found and the parent method throws an exception.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'Language': return $this->strLanguage;
            case 'RootPath': return $this->strRootPath;
            case 'RootUrl': return $this->strRootUrl;
            case 'TempPath': return $this->strTempPath;
            case 'TempUrl': return $this->strTempUrl;
            case 'DateTimeFormat': return $this->strDateTimeFormat;
            case 'IsImageListView': return $this->blnIsImageListView;
            case 'IsListView': return $this->blnIsListView;
            case 'IsBoxView': return $this->blnIsBoxView;
            case 'UpdatedHash': return $this->strUpdatedHash;
            case 'LockedDocuments': return $this->blnLockedDocuments;
            case 'LockedImages': return $this->blnLockedImages;

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
     * Dynamically sets the value of a property by name and updates the corresponding
     * attribute or option in the process. Throws an exception if the value type does not match
     * the expected type for the property or if the property is not recognized.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case 'Language':
                try {
                    $this->strLanguage = Type::cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'language', $this->strLanguage);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'RootPath':
                try {
                    $this->strRootPath = Type::cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rootPath', $this->strRootPath);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'RootUrl':
                try {
                    $this->strRootUrl = Type::cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rootUrl', $this->strRootUrl);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'TempPath':
                try {
                    $this->strTempPath = Type::cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tempPath', $this->strTempPath);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'TempUrl':
                try {
                    $this->strTempUrl = Type::cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tempUrl', $this->strTempUrl);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'DateTimeFormat':
                try {
                    $this->strDateTimeFormat = Type::cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tempUrl', $this->strDateTimeFormat);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'IsImageListView':
                try {
                    $this->blnIsImageListView = Type::cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'isImageListView', $this->blnIsImageListView);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'IsListView':
                try {
                    $this->blnIsListView = Type::cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'isListView', $this->blnIsListView);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'IsBoxView':
                try {
                    $this->blnIsBoxView = Type::cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'isBoxView', $this->blnIsBoxView);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'UpdatedHash':
                try {
                    $this->strUpdatedHash = Type::cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'updatedHash', $this->strUpdatedHash);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'LockedDocuments':
                try {
                    $this->blnLockedDocuments = Type::cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'lockedDocuments', $this->blnLockedDocuments);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'LockedImages':
                try {
                    $this->blnLockedImages = Type::cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'lockedImages', $this->blnLockedImages);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
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

    /**
     * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
     * used by the ModelConnector designer dialog to display a list of options for the control.
     * @return QModelConnectorParam[]
     **/
    public static function getModelConnectorParams(): array
    {
        return array_merge(parent::GetModelConnectorParams(), array());
    }
}