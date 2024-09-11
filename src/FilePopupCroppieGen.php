<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Control;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class PopupCroppieGen
 *
 * @see PopupCroppie
 * @package QCubed\Plugin
 */

/**
 * @property string $Url Default null. If you want to save the cropped image, choose a php file with upload functions.
 * @property string $Language Default null. If you want to choose a language, set the desired language as the language selection.
 * @property string $SelectedImage Default null. Select the image to crop and set it to this variable.
 * @property string $SelectedType Default 'square'. If desired, the default can be set to 'circle.'
 * @property array $Theme Default null. For the default theme, Select 2 displays its own template.
 * @property array $Data Default null.
 * @property string $TranslatePlaceholder Default text '- Select a destination -'. If necessary, the text can be rewritten.
 *
 *
 * See also: http://foliotek.github.io/Croppie/
 *
 * @package QCubed\Plugin
 */

class FilePopupCroppieGen extends Q\Control\Panel
{
    /** @var string */
    protected $strUrl = null;
    /** @var string */
    protected $strLanguage = null;
    /** @var string */
    protected $strSelectedImage = null;
    /** @var string */
    protected $strSelectedType = null;
    /** @var string */
    protected $strTranslatePlaceholder = null;
    /** @var array */
    protected $strTheme = null;
    /** @var array */
    protected $arrData = null;

    protected function makeJqOptions()
    {
        $jqOptions = null;
        if (!is_null($val = $this->AutoOpen)) {$jqOptions['show'] = $val;}
        if (!is_null($val = $this->Url)) {$jqOptions['url'] = $val;}
        if (!is_null($val = $this->Language)) {$jqOptions['language'] = $val;}
        if (!is_null($val = $this->SelectedImage)) {$jqOptions['selectedImage'] = $val;}
        if (!is_null($val = $this->SelectedType)) {$jqOptions['selectedType'] = $val;}
        if (!is_null($val = $this->TranslatePlaceholder)) {$jqOptions['translatePlaceholder'] = $val;}
        if (!is_null($val = $this->Theme)) {$jqOptions['theme'] = $val;}
        if (!is_null($val = $this->Data)) {$jqOptions['data'] = $val;}
        return $jqOptions;
    }

    public function getJqSetupFunction()
    {
        return 'croppieHandler';
    }

    /**
     * Overrides the parent to call the popup-croppie js initializer.
     */

    protected function makeJqWidget()
    {
        Application::executeControlCommand($this->getJqControlId(), "off", Application::PRIORITY_HIGH);
        $jqOptions = $this->makeJqOptions();
        Application::executeControlCommand($this->ControlId, $this->getJqSetupFunction(), $jqOptions,
            Application::PRIORITY_HIGH);
    }

    /**
     * Show the modal.
     **/
    public function showDialogBox()
    {
        Application::executeJavaScript("$('#' + '$this->ControlId').modal('show');");
        $this->Visible = true; // will redraw the control if needed
        $this->Display = true; // will update the wrapper if needed
    }

    /**
     * Hide the modal
     */
    public function hideDialogBox()
    {
        Application::executeJavaScript("$('#' + '$this->ControlId').modal('hide');");
        $this->Visible = false; // will redraw the control if needed
        $this->Display = false; // will update the wrapper if needed
    }

    /**
     * @param $strName
     * @return array|bool|callable|float|int|mixed|string|null
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'Url': return $this->strUrl;
            case 'Language': return $this->strLanguage;
            case 'SelectedImage': return $this->strSelectedImage;
            case 'SelectedType': return $this->strSelectedType;
            case 'TranslatePlaceholder': return $this->strTranslatePlaceholder;
            case 'Theme': return $this->strTheme;
            case 'Data': return json_encode($this->arrData);
            case 'AutoOpen': return $this->blnAutoOpen;
            case 'Show': return $this->blnAutoOpen;

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
            case 'Url':
                try {
                    $this->strUrl = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'url', $this->strUrl);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Language':
                try {
                    $this->strLanguage = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'language', $this->strLanguage);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'SelectedImage':
                try {
                    $this->strSelectedImage = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'selectedImage', $this->strSelectedImage);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'SelectedType':
                try {
                    $this->strSelectedType = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'selectedType', $this->strSelectedType);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'TranslatePlaceholder':
                try {
                    $this->strTranslatePlaceholder = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'translatePlaceholder', $this->strTranslatePlaceholder);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Theme':
                try {
                    $this->strTheme = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'theme', $this->strTheme);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Data':
                try {
                    $this->arrData = Type::Cast($mixValue, Type::ARRAY_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'data', $this->arrData);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case '_IsOpen': // Internal only, to detect when dialog has been opened or closed.
                try {
                    $this->blnIsOpen = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'AutoOpen':    // the JQueryUI name of this option
            case 'Show':    // the Bootstrap name of this option
                try {
                    $this->blnAutoOpen = Type::cast($mixValue, Type::BOOLEAN);
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
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array());
    }
}