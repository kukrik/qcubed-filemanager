<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\ApplicationBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class PopupCroppieGen
 *
 * @see PopupCroppie
 * @package QCubed\Plugin
 */

/**
 * @property string $Url Default null. If you want to save the cropped image, choose a PHP file with upload functions.
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
    /** @var string|null */
    protected ?string $strUrl = null;
    /** @var string|null */
    protected ?string $strLanguage = null;
    /** @var string|null */
    protected ?string $strSelectedImage = null;
    /** @var string|null */
    protected ?string $strSelectedType = null;
    /** @var string|null */
    protected ?string $strTranslatePlaceholder = null;
    /** @var string|null */
    protected ?string $strTheme = null;
    /** @var array|null */
    protected ?array $arrData = null;
    /** @var bool|null */
    protected ?bool $blnIsOpen = null;

    /**
     * Generate an array of jQuery options based on the component's properties.
     *
     * @return array The array of jQuery options derived from the component's properties.
     */
    protected function makeJqOptions(): array
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

    /**
     * Retrieves the jQuery setup function name used for initializing the handler.
     *
     * @return string The name of the jQuery setup function.
     */
    public function getJqSetupFunction(): string
    {
        return 'croppieHandler';
    }

    /**
     * Create and initialize a jQuery widget by applying the setup function with specified options.
     *
     * @return void
     */
    protected function makeJqWidget(): void
    {
        Application::executeControlCommand($this->getJqControlId(), "off", ApplicationBase::PRIORITY_HIGH);
        $jqOptions = $this->makeJqOptions();
        Application::executeControlCommand($this->ControlId, $this->getJqSetupFunction(), $jqOptions,
            ApplicationBase::PRIORITY_HIGH);
    }

    /**
     * Show the modal dialog box.
     *
     * @return void
     * @throws Caller
     */
    public function showDialogBox(): void
    {
        Application::executeJavaScript("$('#' + '$this->ControlId').modal('show');");
        $this->Visible = true; // will redraw the control if needed
        $this->Display = true; // will update the wrapper if needed
    }

    /**
     * Hides the dialog box by executing the necessary JavaScript and updating visibility properties.
     *
     * @return void
     * @throws Caller
     */
    public function hideDialogBox(): void
    {
        Application::executeJavaScript("$('#' + '$this->ControlId').modal('hide');");
        $this->Visible = false; // will redraw the control if needed
        $this->Display = false; // will update the wrapper if needed
    }

    /**
     * Magic getter method to retrieve property values based on the provided property name.
     *
     * @param string $strName The name of the property to retrieve.
     *
     * @return mixed Returns the corresponding property value, JSON-encoded data for arrays,
     *               or delegates to the parent::__get for undefined properties.
     * @throws Caller Throws an exception if the property is undefined and the parent::__get also fails.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'Url': return $this->strUrl;
            case 'Language': return $this->strLanguage;
            case 'SelectedImage': return $this->strSelectedImage;
            case 'SelectedType': return $this->strSelectedType;
            case 'TranslatePlaceholder': return $this->strTranslatePlaceholder;
            case 'Theme': return $this->strTheme;
            case 'Data': return json_encode($this->arrData);
            case 'Show':
            case 'AutoOpen': return $this->blnAutoOpen;

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
     * Handles setting the value of a property dynamically based on the provided name and value.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the specified property. It will be validated and cast to the appropriate type.
     *
     * @return void
     * @throws InvalidCast Throws an exception if the value cannot be cast to the expected type.
     * @throws Caller Throws an exception if the property is not recognized or cannot be set.
     */
    public function __set(string $strName, mixed $mixValue): void
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
            case '_IsOpen': // Internal only to detect when a dialog has been opened or closed.
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
     * Retrieves the model connector parameters by merging the parent parameters with additional parameters.
     *
     * @return array A combined array of model connector parameters.
     */
    public static function getModelConnectorParams(): array
    {
        return array_merge(parent::GetModelConnectorParams(), array());
    }
}