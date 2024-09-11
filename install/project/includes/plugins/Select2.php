<?php

/**
 * The Select2 override file. This file gets installed into project/includes/plugins/select2
 * duing the initial installation of the plugin. After that, it is not touched.
 * Feel free to modify this file as needed.
 *
 * @see Select2Base
 */

namespace QCubed\Plugin;

class Select2 extends Select2ListBoxBase
{
    public function  __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->removeCssClass("listbox");
        $this->registerFiles();
    }

    protected function registerFiles()
    {
        $this->addJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/select2.min.js");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2-bootstrap.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2-web-vauu.css");
    }
}
