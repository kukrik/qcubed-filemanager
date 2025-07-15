<?php

/**
 * The Select2 override file. This file gets installed into project/includes/plugins/select2
 *  during the initial installation of the plugin. After that, it is not touched.
 * Feel free to modify this file as needed.
 *
 * @see Select2Base
 */

namespace QCubed\Plugin;

use QCubed\Exception\Caller;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;

/**
 * A Select2 wrapper class extending Select2ListBoxBase.
 *
 * This class incorporates the Select2 plugin, enhancing the user interface
 * for dropdown list controls. It includes default styling and script files
 * required for proper functionality and allows further customization if needed.
 *
 * The initial set of assets (JavaScript and CSS) is registered automatically
 * upon instantiation of the class.
 */
class Select2 extends Select2ListBoxBase
{
    /**
     * ListBoxBase constructor.
     * @param ControlBase|FormBase $objParentObject
     * @param string|null $strControlId
     * @throws Caller
     */
    public function  __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->removeCssClass("listbox");
        $this->registerFiles();
    }

    /**
     * Registers the necessary JavaScript and CSS files for the control.
     *
     * @return void
     * @throws Caller
     */
    protected function registerFiles(): void
    {
        $this->addJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/select2.min.js");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2-bootstrap.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2-web-vauu.css");
    }
}
