<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Control;
use QCubed\Bootstrap as Bs;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class FileManagerBaseGen
 *
 * @see FileUploadBase
 * @package QCubed\Plugin
 */

/**
 * @property string $Language	Default: empty. Optional language selection, default is set to English (en).
 *
 * @package QCubed\Plugin
 */

class FileManagerBaseGen extends Q\Control\Panel
{
    /** @var string */
    // protected $str = null;


    protected function makeJqOptions()
    {
//        $jqOptions = parent::MakeJqOptions();
//        if (!is_null($val = $this->Blaa)) {$jqOptions['blaa'] = $val;}
//
//        return $jqOptions;
    }

    public function getJqSetupFunction()
    {
        return 'filemanager';
    }

    public function __get($strName)
    {
        switch ($strName) {
            // case 'Language': return $this->strBlaa;


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
//            case 'Blaa':
//                try {
//                    $this->strLanguage = Type::Cast($mixValue, Type::STRING);
//                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'blaa', $this->strLanguage);
//                    break;
//                } catch (InvalidCast $objExc) {
//                    $objExc->incrementOffset();
//                    throw $objExc;
//                }



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


