<?php

namespace Sandstorm\CrudForms\ViewHelpers\Internal;

use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

class FindObjectsForListingViewHelper extends AbstractViewHelper
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
        $this->registerArgument('repository', 'string', 'Repository', true);
    }

    /**
     */
    public function render()
    {
        $repository = $this->arguments['repository'];
        if (strpos($repository, '::') === false) {
            $repositoryName = $repository;
            $methodName = 'findAll';
        } else {
            list($repositoryName, $methodName) = explode('::', $repository);
        }

        return $this->objectManager->get($repositoryName)->$methodName();
    }
}
