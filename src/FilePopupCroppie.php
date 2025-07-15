<?php

namespace QCubed\Plugin;

use QCubed\Bootstrap\Bootstrap;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class PopupCroppie
 *
 * @property string $HeaderTitle Default header title "Crop image". The title can be overridden if desired.
 * @property string $HeaderClass Default header background class "bg-default". The background class can be overridden
 *                                  if desired.
 * @property string $RotateClass Default "btn-default" class for "Rotate left" and "Rotate right" buttons. A common
 *                                  button class can be overridden if desired.
 * @property string $SaveClass Default "Crop and save" button background class "btn-orange". The background class of
 *                              the button can be overridden if desired.
 * @property string $SaveText Default button text "Crop and save". The button text can be overridden if desired.
 * @property string $CancelClass Default "btn-default" background class for the "Cancel" button. The background class of
 *                                  the button can be overridden if desired.
 * @property string $CancelText Default button text "Cancel". The button text can be overridden if desired.
 * @property string $FinalPath Default null. Outputs the name of the cropped image along with the relative path after saving.
 *
 *
 * @package QCubed\Plugin
 */

class FilePopupCroppie extends FilePopupCroppieGen
{
    /** @var bool make sure the popupCroppie gets rendered */
    protected bool $blnAutoRender = true;
    /** @var bool default to auto open being false, since this would be a rare need, and dialogs are auto-rendered. */
    protected $blnAutoOpen = false;
    /** @var bool records whether the dialog is open */
    protected ?bool $blnIsOpen = false;
    protected ?bool $blnIsChangeObject = false;
    protected bool $blnUseWrapper = true;

    /** @var string */
    protected string $strHeaderTitle = 'Crop image';
    /** @var string */
    protected string $strHeaderClass = 'bg-default';
    /** @var string */
    protected string $strRotateClass = 'btn-default';
    /** @var string */
    protected string $strSaveClass = 'btn-orange';
    /** @var string */
    protected string $strSaveText = 'Crop and save';
    /** @var string */
    protected string $strCancelClass = 'btn-default';
    /** @var string */
    protected string $strCancelText = 'Cancel';
    /** @var string|null */
    protected ?string $strFinalPath = null;

    /**
     * Constructor for the class.
     *
     * @param mixed $objParentObject The parent object which this control belongs to, or null to set it to the global
     *     form.
     * @param string|null $strControlId The control ID to uniquely identify this control, or null to generate an ID
     *     automatically.
     *
     * @return void
     * @throws Caller
     */
    public function __construct(mixed $objParentObject = null, ?string $strControlId = null)
    {
        // Detect which mode we are going to display in, whether to show right away or wait for later.
        if ($objParentObject === null) {
            // The dialog will be shown right away, and then when closed, removed from the form.
            global $_FORM;
            $objParentObject = $_FORM;    // The parent object should be the form. Prevents spurious redrawing.
            $this->blnDisplay = true;
            $this->blnAutoOpen = true;
        } else {
            $this->blnAutoOpen = false;
            $this->blnDisplay = false;
        }

        parent::__construct($objParentObject, $strControlId);
        $this->registerFiles();
    }

    /**
     * Registers the CSS and JavaScript files required for the file manager functionality.
     *
     * @return void
     * @throws Caller
     */
    protected function registerFiles(): void
    {
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/croppie.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/custom-switch.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/custom.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/font-awesome.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/awesome-bootstrap-checkbox.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2.css");
        $this->addCssFile(QCUBED_BOOTSTRAP_CSS);
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2-web-vauu.css");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.croppie.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/croppie.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/exif.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/select2.js");
        Bootstrap::loadJS($this);
    }

    /**
     * Generates and returns the HTML markup for the control.
     *
     * The returned HTML includes the structure for a modal dialog with various
     * components such as a header, body, options for viewport configuration,
     * buttons for actions like save, cancel, and rotation, as well as selections
     * for a destination and viewport type.
     *
     * @return string The generated HTML markup for the control.
     */
    public function getControlHtml(): string
    {
        $strHtml = '';

        $strHtml .= _nl('<div id="' . $this->ControlId . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">');
        $strHtml .= _nl(_indent('<div class="modal-dialog modal-lg" role="document" tabindex="-1">', 1));
        $strHtml .= _nl(_indent('<div class="modal-content">', 2));
        $strHtml .= _nl(_indent('<div class="modal-header ' . $this->strHeaderClass . '">', 3));
        $strHtml .= _nl(_indent('<button type="button" class="close" data-dismiss="modal" aria-label="Close">', 4));
        $strHtml .= _nl(_indent('<span aria-hidden="true">×</span>', 5));
        $strHtml .= _nl(_indent('</button>', 4));
        $strHtml .= _nl(_indent('<h4 class="modal-title" id="gridSystemModalLabel">' . t($this->strHeaderTitle) . '</h4>', 4));
        $strHtml .= _nl(_indent('</div>', 3));
        $strHtml .= _nl(_indent('<div class="modal-body">', 3));
        $strHtml .= _nl(_indent('<div class="row">', 4));
        $strHtml .= _nl(_indent('<div class="col-md-6">', 5));
        $strHtml .= _nl(_indent('<div class="img-responsive">', 6));
        $strHtml .= _nl(_indent('<div id="cropImage"></div>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('</div>', 5));
        $strHtml .= _nl(_indent('<div class="col-md-6">', 5));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Viewport:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-4">', 6));
        $strHtml .= _nl(_indent('<input id="viewportWidth" class="form-control" value="200" max="330" autocomplete="off" type="text" placeholder="' . t('Width') . '">', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-4">', 6));
        $strHtml .= _nl(_indent('<input id="viewportHeight" class="form-control" value="200" max="330" autocomplete="off" type="text" placeholder="' . t('Height') . '">', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Enable resize:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-8">', 6));
        $strHtml .= _nl(_indent('<div class="switch" id="enable-type">', 7));
        $strHtml .= _nl(_indent(t('Inactive'),8));
        $strHtml .= _nl(_indent('<label>', 8));
        $strHtml .= _nl(_indent('<input type="checkbox">', 9));
        $strHtml .= _nl(_indent('<span class="web-vauu"></span>', 9));
        $strHtml .= _nl(_indent('</label>', 8));
        $strHtml .= _nl(_indent(t('Active'),8));
        $strHtml .= _nl(_indent('</div>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Viewport type:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-8">', 6));
        $strHtml .= _nl(_indent('<select class="web-vauu-type" id="webVauuType" name="webVauuType" style="width:100%">', 7));
        $strHtml .= _nl(_indent('<option value="square" selected="selected">' . t('square') . '</option>', 8));
        $strHtml .= _nl(_indent('<option value="circle">' . t('circle') . '</option>', 8));
        $strHtml .= _nl(_indent('</select>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Rotate:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-4">', 6));
        $strHtml .= _nl(_indent('<button type="button" class="btn ' .  $this->strRotateClass . ' rotate-left" data-deg="-90">' . t('Rotate left') . '</button>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-4">', 6));
        $strHtml .= _nl(_indent('<button type="button" class="btn ' .  $this->strRotateClass . ' rotate-right" data-deg="90">' . t('Rotate right') . '</button>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Destination:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-8">', 6));
        $strHtml .= _nl(_indent('<select class="web-vauu-destination" id="webVauuDestination" name="webVauuDestination" style="width:100%">', 7));
        $strHtml .= _nl(_indent('<option></option>', 8));
        $strHtml .= _nl(_indent('</select>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('</div>', 5));
        $strHtml .= _nl(_indent('</div>', 4));
        $strHtml .= _nl(_indent('</div>', 3));
        $strHtml .= _nl(_indent('<div class="modal-footer">', 3));
        $strHtml .= _nl(_indent('<button type="button" class="btn ' . $this->strSaveClass . ' btn-crop">' . t($this->strSaveText) . '</button>', 4));
        $strHtml .= _nl(_indent('<button type="button" class="btn ' . $this->strCancelClass . '" data-btnid="Cancel" data-dismiss="modal">' . t($this->strCancelText) . '</button>', 4));
        $strHtml .= _nl(_indent('</div>', 3));
        $strHtml .= _nl(_indent('</div>', 2));
        $strHtml .= _nl(_indent('</div>', 1));
        $strHtml .= '</div>';

        return $strHtml;
    }

    /**
     * Magic method to get the value of a property dynamically.
     *
     * @param string $strName The name of the property to retrieve.
     *
     * @return mixed The value of the requested property.
     * @throws Caller If the requested property does not exist or cannot be retrieved.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "HeaderTitle": return $this->strHeaderTitle;
            case "HeaderClass": return $this->strHeaderClass;
            case "RotateClass": return $this->strRotateClass;
            case "SaveClass": return $this->strSaveClass;
            case "SaveText": return $this->strSaveText;
            case "CancelClass": return $this->strCancelClass;
            case "CancelText": return $this->strCancelText;
            case 'FinalPath': return $this->strFinalPath;

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
     * Magic method to set the value of a property.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     * @throws InvalidCast If the provided value cannot be cast to the expected type.
     * @throws Caller If the property name is not valid or cannot be set.
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case '_finalPath': // Internal only to output the cropped image name with a relative path after saving.
                try {
                    $this->strFinalPath = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case '_IsChangeObject': // Internal only to detect when recording is triggered.
                try {
                    $this->blnIsChangeObject = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "HeaderTitle":
                try {
                    $this->strHeaderTitle = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "HeaderClass":
                try {
                    $this->strHeaderClass = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "RotateClass":
                try {
                    $this->strRotateClass = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "SaveClass":
                try {
                    $this->strSaveClass = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "SaveText":
                try {
                    $this->strSaveText = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "CancelClass":
                try {
                    $this->strCancelClass = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "CancelText":
                try {
                    $this->strCancelText = Type::Cast($mixValue, Type::STRING);
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
