<?php

namespace QCubed\Plugin;

use QCubed\Exception\Caller;

class FileManagerBase extends FileManagerBaseGen
{
    public function  __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->registerFiles();
        // $this->setup();
    }

    /**
     * @throws Caller
     */
    protected function registerFiles() {
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.filemanager.js");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.filemanager.css");
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
    }


}
