<?php
namespace Sandstorm\CrudForms\Annotations;
/**
 * @Annotation
 * @Target("CLASS")
 */
final class FormFieldGroup
{

    public $group;
    public $label;
    public $position; // position string as understood by positional array sorter

    public $visible = TRUE;
    public $visibleInOverview = TRUE;
    public $visibleInForm = TRUE;
    public $readonly = FALSE;

    // generic "configuration" block to be used for specific templates
    public $configuration;
    public $hasFields = FALSE;
    public $fields = [];
    public $groups = [];
    public $cssClassesFrontEnd;    
    public $cssClassesBackEnd;
}
