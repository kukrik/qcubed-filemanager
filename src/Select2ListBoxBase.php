<?php

namespace QCubed\Plugin;

/**
 * This class extends Select2ListBoxBaseGen and provides additional functionality
 * specific to managing select2-based list box controls.
 */
class Select2ListBoxBase extends Select2ListBoxBaseGen
{
    /**
     * Retrieves the jQuery control ID of the current object.
     *
     * @return string The jQuery control ID associated with this object.
     */
    public function getJqControlId(): string
    {
        return $this->ControlId;
    }

    /**
     * Generates and retrieves the HTML content for the reset button.
     *
     * @return string The HTML string for the reset button.
     */
    public function getResetButtonHtml(): string
    {
        return "";
    }
}

