<?php

namespace Sandstorm\CrudForms\ViewHelpers\Internal;

use Neos\Utility\ObjectAccess;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ObjectAccessViewHelper
 * @package Sandstorm\CrudForms\ViewHelpers
 */
class ObjectAccessViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     *
     * @return void
     * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('property', 'string', 'Property', true);
    }

    /**
     */
    public function render()
    {
        $object = $this->renderChildren();
        return ObjectAccess::getPropertyPath($object, $this->arguments['property']);
    }
}
