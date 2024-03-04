<?php

namespace QCubed\Plugin\Event;

use QCubed\Event\EventBase;

/**
 * Class ImageSave
 *
 * Captures the save event that occurs after the popup is closed.
 *
 */

class ImageSave extends EventBase {

    const EVENT_NAME = 'imagesave';
    const JS_RETURN_PARAM = 'ui';
}
