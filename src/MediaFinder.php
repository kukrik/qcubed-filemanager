<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Control\FormBase;
use QCubed\Control\ControlBase;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\Project\Application;
//use QCubed\Js;
use QCubed\Type;
/**
 * Class MediaFinder
 *
 * @property string $TempPath Default temp path APP_UPLOADS_TEMP_DIR. If necessary, the temp dir must be specified.
 * @property string $EmptyImagePath Default predefined image, can be overridden and replaced with another image if desired
 * @property string $EmptyImageAlt Default null. The recommendation is to add the following text: "Choose a picture"
 * @property integer $SelectedImageId Default null. In the case of a selected image, the id of the image is pushed,
 *                                 as well as the id of the selected image is transferred to the database in the column
 *                                 of the selected table.
 * @property string $SelectedImagePath Default null. The path of the selected image with the file name
 * @property string $SelectedImageName Default null. The file name of the selected image
 * @property string $SelectedImageAlt Default null. The recommendation is to add the following text: "Selected picture"
 *
 * @package QCubed\Plugin
 */

class MediaFinder extends MediaFinderGen
{
    /** @var string */
    protected $intItem = null;
    /** @var string */
    protected $strTempUrl = APP_UPLOADS_TEMP_URL;
    /** @var string EmptyImagePath */
    protected $strEmptyImagePath = QCUBED_FILEMANAGER_ASSETS_URL . "/images/empty-images-icon.png";
    /** @var string EmptyImageAlt */
    protected $strEmptyImageAlt = null;
    /** @var integer SelectedimageId */
    protected $intSelectedImageId = null;
    /** @var string SelectedImagePath */
    protected $strSelectedImagePath = null;
    /** @var string SelectedImageName */
    protected $strSelectedImageName = null;
    /** @var string SelectedImageAlt */
    protected $strSelectedImageAlt = null;

    /**
     * @param $objParentObject
     * @param $strControlId
     * @throws Caller
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->registerFiles();
    }

    /**
     * @throws Caller
     */
    protected function registerFiles()
    {
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.mediafinder.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/jquery.slimscroll.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/custom.js");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.mediafinder.css");
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
    }

    /**
     * Returns the HTML for the control.
     *
     * @return string
     */
    protected function getControlHtml()
    {
        $strHtml = '';

        $strHtml .= '<div class="image-container">';
        $strHtml .= $this->chooseImageTemplate();
        $strHtml .= $this->selectedImageTemplate();
        $strHtml .= '</div>';

        return $strHtml;
    }

    protected function chooseImageTemplate()
    {
        $strHtml = '';

        if (!$this->intSelectedImageId) {
            $strHtml .= _nl(_indent('<div class="choose-image">', 1));
        } else {
            $strHtml .= _nl(_indent('<div class="choose-image hidden">', 1));
        }

        if ($this->strEmptyImageAlt) {
            $strHtml .= _nl(_indent('<img src="' . $this->strEmptyImagePath . '" alt="' . $this->strEmptyImageAlt . '" class="image img-responsive">', 2));
        } else {
            $strHtml .= _nl(_indent('<img src="' . $this->strEmptyImagePath . '" class="image img-responsive">', 2));
        }

        $strHtml .= _nl(_indent('</div>', 1));

        return $strHtml;
    }

    protected function selectedImageTemplate()
    {
        $strHtml = '';

        if (!$this->intSelectedImageId) {
            $strHtml .= _nl(_indent( '<div id="' . $this->ControlId . '" class="selected-image hidden">', 1));
        } else {
            $strHtml .= _nl(_indent( '<div id="' . $this->ControlId . '" class="selected-image">', 1));
        }

        if ($this->strSelectedImageAlt) {
            $strHtml .= _nl(_indent('<img src="' . $this->strSelectedImagePath . '" data-id ="' . $this->intSelectedImageId . '" data-event= "save" alt="' . $this->strSelectedImageAlt . '" class="image overlay-path img-responsive">', 2));
        } else {
            $strHtml .= _nl(_indent('<img src="' . $this->strSelectedImagePath . '" data-id ="' . $this->intSelectedImageId . '" data-event= "save" class="image overlay-path img-responsive">', 2));
        }

        $strHtml .= _nl(_indent('<div id="' . $this->ControlId . '"  class="overlay" data-id ="' . $this->intSelectedImageId . '" data-event= "delete">', 3));
        if ($this->strSelectedImageName) {
            $strHtml .= _nl(_indent('<span class="overLay-left">' . $this->strSelectedImageName . '</span>', 4));
        } else {
            $strHtml .= _nl(_indent('<span class="overLay-left"></span>', 4));
        }

        $strHtml .= _nl(_indent('<span class="overLay-right">', 4));
        $strHtml .= _nl(_indent('<svg viewBox="-15 -15 56 56" class="svg-delete files-svg">', 5));
        $strHtml .= _nl(_indent('<path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"></path>', 6));
        $strHtml .= _nl(_indent('</svg>', 5));
        $strHtml .= _nl(_indent('</span>', 4));
        $strHtml .= _nl(_indent('</div>', 2));
        $strHtml .= _nl(_indent('</div>', 1));

        return $strHtml;
    }

    public function getEndScript()
    {
        $strJS = parent::getEndScript();

        $strCtrlJs = <<<FUNC
$(document).ready(function() {
    var choose_image = document.querySelector(".choose-image");
    var selected_image = document.querySelector(".selected-image");
    var overlay = document.querySelector(".overlay");
    var overlay_path = document.querySelector(".overlay-path");
    var overlay_left = document.querySelector(".overLay-left");
    
    function getDataParams(params) {
        var data = JSON.parse(params);
        var id = data.id;
        var name = data.name;
        var path = data.path;
        
        if (id && name && path) {
            choose_image.classList.add('hidden');
            selected_image.classList.remove('hidden');
            overlay.setAttribute('data-id', id);
            overlay_path.setAttribute('data-id', id);
            overlay_path.src = '$this->strTempUrl' + path;
            overlay_left.textContent = name;
        } else {
            choose_image.classList.remove('hidden');
            selected_image.classList.add('hidden');
            overlay.setAttribute('data-id', '');
            overlay_path.setAttribute('data-id', '');
            overlay_path.src = "";
        }
        
       imageSave();
    }

    window.getDataParams = getDataParams;

    imageSave = function() {
        var overlay_path = $(".overlay-path");
        overlay_path.on("imagesave", function(event) {
            if (overlay_path.data('id') !== "" && overlay_path.data('event') === 'save') {
                qcubed.recordControlModification("$this->ControlId", "_Item", overlay_path.data('id'));
            }
        });

        var ImageSaveEvent = $.Event("imagesave");
        overlay_path.trigger(ImageSaveEvent);
    }
    
    $(".overlay").on("click", function() {
        var id = overlay.getAttribute('data-id')

        choose_image.classList.remove('hidden');
        selected_image.classList.add('hidden');
        overlay.setAttribute('data-id', '');
        overlay_path.setAttribute('data-id', '');
        overlay_path.src = '';
        overlay_left.textContent = '';
        
        imageDelete();
    });
    
    imageDelete = function() {
        var overlay = $(".overlay");
        overlay.on("imagedelete", function(event) {
            if (overlay.data('id') !== "" && overlay.data('event') === 'delete') {
                qcubed.recordControlModification("$this->ControlId", "_Item", overlay.data('id'));
            }
        });

        var ImageDeleteEvent = $.Event("imagedelete");
        overlay.trigger(ImageDeleteEvent);
    } 
});
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
            case 'Item': return $this->intItem;
            case "TempUrl": return $this->strTempUrl;
            case "EmptyImagePath": return $this->strEmptyImagePath;
            case "EmptyImageAlt": return $this->strEmptyImageAlt;
            case 'SelectedImageId': return $this->intSelectedImageId;
            case "SelectedImagePath": return $this->strSelectedImagePath;
            case "SelectedImageName": return $this->strSelectedImageName;
            case "SelectedImageAlt": return $this->strSelectedImageAlt;

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
            case "_Item": // Internal only. Do not use. Used by JS above to track selections.
                try {
                    $data = Type::cast($mixValue, Type::INTEGER);
                    $this->intItem = $data;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
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
            case "EmptyImagePath":
                try {
                    $this->strEmptyImagePath = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "EmptyImageAlt":
                try {
                    $this->strEmptyImageAlt = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "SelectedImageId":
                try {
                    $this->intSelectedImageId = Type::Cast($mixValue, Type::INTEGER);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "SelectedImagePath":
                try {
                    $this->strSelectedImagePath = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "SelectedImageName":
                try {
                    $this->strSelectedImageName = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "SelectedImageAlt":
                try {
                    $this->strSelectedImageAlt = Type::Cast($mixValue, Type::STRING);
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