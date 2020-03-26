<?php

namespace Sandstorm\CrudForms\ViewHelpers\Internal;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Reflection\ReflectionService;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

class ActionExistsViewHelper extends AbstractViewHelper
{

    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * Initialize arguments
     *
     * @return void
     * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('action', 'string', 'Action', true);
    }


    /**
     */
    public function render()
    {
        $controllerObjectName = $this->controllerContext->getRequest()->getControllerObjectName();
        return $this->reflectionService->hasMethod($controllerObjectName, $this->arguments['action'] . 'Action');
    }
}
