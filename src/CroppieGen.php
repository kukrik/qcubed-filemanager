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
 * Class CroppieGen
 *
 * @see Croppie
 * @package QCubed\Plugin
 */

/**
 * ## OPTIONS ##
 *
 * @property object $Boundary Default: will default to the size of the container. The outer container of the cropper.
 * @property string $CustomClass Default: ''. A class of your choosing to add to the container to add custom styles to your croppie.
 * @property boolean $EnableExif Default: false. Enable exif orientation reading. Tells Croppie to read exif orientation
 *                                  from the image data and orient the image correctly before rendering to the page.
 * @property boolean $EnableOrientation Default: false. Enable or disable support for specifying a custom orientation
 *                                          when binding images (See bind method).
 * @property boolean $EnableResize Default: false. Enable or disable support for resizing the viewport area.
 * @property boolean $EnableZoom Default: true. Enable zooming functionality. If set to false - scrolling and pinching would not zoom.
 * @property boolean $EnforceBoundary Default: true, /*Experimental/. Restricts zoom so image cannot be smaller than viewport.
 * @property boolean $MouseWheelZoom Default: true. Enable or disable the ability to use the mouse wheel to zoom in and
 *                                      out on a croppie instance. If 'ctrl' is passed mouse wheel will only work while
 *                                      control keyboard is pressed
 * @property boolean $ShowZoomer Default: true. Hide or Show the zoom slider.
 * @property object $Viewport Default: { width: 100, height: 100, type: 'square' }. The inner container of the coppie.
 *                              The visible part of the image. Valid type values:'square' 'circle'.
 * @property string $Url Default: null. Image path.

 *
 * See also: http://foliotek.github.io/Croppie/
 *
 * @package QCubed\Plugin
 */

class CroppieGen extends Q\Control\Panel
{
    /** @var array */
    protected $arrBoundary = null;
    /** @var string */
    protected $strCustomClass = null;
    /** @var boolean */
    protected $blnEnableExif = null;
    /** @var boolean */
    protected $blnEnableOrientation = null;
    /** @var boolean */
    protected $blnEnableResize = null;
    /** @var boolean */
    protected $blnEnableZoom = null;
    /** @var boolean */
    protected $blnEnforceBoundary = null;
    /** @var boolean */
    protected $blnMouseWheelZoom = null;
    /** @var boolean */
    protected $blnShowZoomer = null;
    /** @var array */
    protected $arrViewport = null;
    /** @var string */
    protected $strUrl = null;

    protected function makeJqOptions()
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Boundary)) {$jqOptions['boundary'] = $val;}
        if (!is_null($val = $this->CustomClass)) {$jqOptions['customClass'] = $val;}
        if (!is_null($val = $this->EnableExif)) {$jqOptions['enableExif'] = $val;}
        if (!is_null($val = $this->EnableOrientation)) {$jqOptions['enableOrientation'] = $val;}
        if (!is_null($val = $this->EnableResize)) {$jqOptions['enableResize'] = $val;}
        if (!is_null($val = $this->EnableZoom)) {$jqOptions['enableZoom'] = $val;}
        if (!is_null($val = $this->EnforceBoundary)) {$jqOptions['enforceBoundary'] = $val;}
        if (!is_null($val = $this->MouseWheelZoom)) {$jqOptions['mouseWheelZoom'] = $val;}
        if (!is_null($val = $this->ShowZoomer)) {$jqOptions['showZoomer'] = $val;}
        if (!is_null($val = $this->Viewport)) {$jqOptions['viewport'] = $val;}
        if (!is_null($val = $this->Url)) {$jqOptions['url'] = $val;}
        return $jqOptions;
    }

    public function getJqSetupFunction()
    {
        return 'croppie';
    }

    /**
     * Get the crop points, and the zoom of the image.
     *
     * * This method does not accept any arguments.
     */
    public function get()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "get", Application::PRIORITY_LOW);
    }

    /**
     *  Bind an image to the croppie. Returns a promise
     *                           to be resolved when the image has been loaded and the croppie has been initialized.
     *                           Parameters
     *                               url URL to image
     *                               points Array of points that translate into [topLeftX, topLeftY, bottomRightX, bottomRightY]
     *                               zoom Apply zoom after image has been bound
     *                               orientation Custom orientation, applied after exif orientation (if enabled).
     *                               Only works with enableOrientation option enabled (see 'Options').
     *                           Valid options are:
     *                               1 unchanged
     *                               2 flipped horizontally
     *                               3 rotated 180 degrees
     *                               4 flipped vertically
     *                               5 flipped horizontally, then rotated left by 90 degrees
     *                               6 rotated clockwise by 90 degrees
     *                               7 flipped horizontally, then rotated right by 90 degrees
     *                               8 rotated counter-clockwise by 90 degrees
     *
     * @param $options
     *
     * * This method does not accept any arguments.
     */
    public function bind($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "bind", $options, Application::PRIORITY_LOW);
    }

    /**
     * Destroy a croppie instance and remove it from the DOM
     *
     * * This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }

    /**
     *  Get the resulting crop of the image.
     *                           to be resolved when the image has been loaded and the croppie has been initialized.
     *                           Parameters
     *                              'type' The type of result to return defaults to 'canvas'
     *                                  'base64' returns a the cropped image encoded in base64
     *                                  'html' returns html of the image positioned within an div of hidden overflow
     *                                  'blob' returns a blob of the cropped image
     *                                  'rawcanvas' returns the canvas element allowing you to manipulate prior to getting the resulted image
     *                              'size' The size of the cropped image defaults to 'viewport'
     *                                  'viewport' the size of the resulting image will be the same width and height as the viewport
     *                                  'original' the size of the resulting image will be at the original scale of the image
     *                                  {width, height} an object defining the width and height. If only one dimension is specified, the other will be calculated using the viewport aspect ratio.
     *                              'format' Indicating the image format.
     *                                  Default:'png'
     *                                  Valid values:'jpeg'|'png'|'webp'
     *                              'quality' Number between 0 and 1 indicating image quality.
     *                                  Default:1
     *                              'circle' force the result to be cropped into a circle
     *                                  Valid Values:true | false
     *
     * @param $parameters
     *
     * * This method does not accept any arguments.
     */
    public function result($parameters)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "result", $parameters, Application::PRIORITY_LOW);
    }
    /**
     *  Rotate the image by a specified degree amount. Only works with enableOrientation option enabled (see 'Options').
     *                              'degrees' Valid Values:90, 180, 270, -90, -180, -270
     *
     * @param $degrees
     *
     * * This method does not accept any arguments.
     */

    public function rotate($degrees)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "rotate", $degrees, Application::PRIORITY_LOW);
    }

    /**
     *  Set the zoom of a Croppie instance. The value passed in is still restricted to the min/max set by Croppie.
     * 'value' a floating point to scale the image within the croppie. Must be between a min and max value set by croppie.
     *
     * @param $value
     *
     * * This method does not accept any arguments.
     */
    public function setZoom($value)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "setZoom", $value, Application::PRIORITY_LOW);
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'Boundary': return $this->arrBoundary;
            case 'CustomClass': return $this->strCustomClass;
            case 'EnableExif': return $this->blnEnableExif;
            case 'EnableOrientation': return $this->blnEnableOrientation;
            case 'EnableResize': return $this->blnEnableResize;
            case 'EnableZoom': return $this->blnEnableZoom;
            case 'EnforceBoundary': return $this->blnEnforceBoundary;
            case 'MouseWheelZoom': return $this->blnMouseWheelZoom;
            case 'ShowZoomer': return $this->blnShowZoomer;
            case 'Viewport': return $this->arrViewport;
            case 'Url': return $this->strUrl;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'Boundary':
                try {
                    $this->arrBoundary = Type::Cast($mixValue, Type::ARRAY_TYPE);
                    $this->arrBoundary = $mixValue;
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'boundary', $this->arrBoundary);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'CustomClass':
                try {
                    $this->strCustomClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'customClass', $this->strCustomClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'EnableExif':
                try {
                    $this->blnEnableExif = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enableExif', $this->blnEnableExif);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'EnableOrientation':
                try {
                    $this->blnEnableOrientation = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enableOrientation', $this->blnEnableOrientation);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'EnableResize':
                try {
                    $this->blnEnableResize = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enableResize', $this->blnEnableResize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'EnableZoom':
                try {
                    $this->blnEnableZoom = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enableZoom', $this->blnEnableZoom);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'EnforceBoundary':
                try {
                    $this->blnEnforceBoundary = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enforceBoundary', $this->blnEnforceBoundary);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'MouseWheelZoom':
                try {
                    $this->blnMouseWheelZoom = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'mouseWheelZoom', $this->blnMouseWheelZoom);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ShowZoomer':
                try {
                    $this->blnShowZoomer = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'showZoomer', $this->blnShowZoomer);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Viewport':
                try {
                    $this->arrViewport = Type::Cast($mixValue, Type::ARRAY_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'viewport', $this->arrViewport);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Url':
                try {
                    $this->strUrl = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'url', $this->strUrl);
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


