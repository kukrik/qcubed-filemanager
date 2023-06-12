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
 * @see FileManagerBase
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
    protected $strLanguage = null;


    protected function makeJqOptions()
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Language)) {$jqOptions['language'] = $val;}

        return $jqOptions;
    }

    public function getJqSetupFunction()
    {
        return 'fileManager';
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'Language': return $this->strLanguage;


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
            case 'Language':
                try {
                    $this->strLanguage = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'language', $this->strLanguage);
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


