<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Control;
use QCubed\Project\Control\ControlBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class MediaFinderGen
 * @see MediaFinder
 * @package QCubed\Plugin
 */

/**
 * @property string $PopupUrl
 * @property string $PopupWidth
 * @property string $PopupHeight
 *
 * @package QCubed\Plugin
 */

class MediaFinderGen extends Q\Control\Panel
{
    protected $strPopupUrl = null;
    protected $strPopupWidth = null;
    protected $strPopupHeight = null;

    protected function makeJqOptions()
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->PopupUrl)) {$jqOptions['url'] = $val;}
        if (!is_null($val = $this->PopupWidth)) {$jqOptions['popupWidth'] = $val;}
        if (!is_null($val = $this->PopupHeight)) {$jqOptions['popupHeight'] = $val;}
        return $jqOptions;
    }

    protected function getJqSetupFunction()
    {
        return 'mediaFinder';
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'PopupUrl': return $this->strPopupUrl;
            case 'PopupWidth': return $this->strPopupWidth;
            case 'PopupHeight': return $this->strPopupHeight;

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
            case 'PopupUrl':
                try {
                    $this->strPopupUrl = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'popupUrl', $this->strPopupUrl);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'PopupWidth':
                try {
                    $this->strPopupWidth = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'popupWidth', $this->strPopupWidth);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'PopupHeight':
                try {
                    $this->strPopupHeight = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'popupHeight', $this->strPopupHeight);
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
}