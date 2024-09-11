<?php

namespace QCubed\Plugin;

use QCubed\Control\ControlBase;


/**
 * Class Croppie
 * @package QCubed\Plugin
 */

class Croppie extends CroppieGen
{
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
    protected function registerFiles() {
        $this->AddJavascriptFile(QCUBED_CROPPIE_ASSETS_URL . "/js/croppie.js");
        $this->AddJavascriptFile(QCUBED_CROPPIE_ASSETS_URL . "/js/exif.js");
        $this->addCssFile(QCUBED_CROPPIE_ASSETS_URL . "/css/croppie.css");
    }
}
