<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Project\Control\ControlBase;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Application;


class Select2ListBoxBase extends Select2ListBoxBaseGen
{
    public function getJqControlId()
    {
        return $this->ControlId;
    }

    public function getResetButtonHtml()
    {
        return "";
    }
}

