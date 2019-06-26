<?php
namespace Sandstorm\CrudForms\Annotations;
/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
Abstract class AbstractFormField
{
    
    /**
     */
    public function __construct()
    {
        $this->collectionElementFormFields = array();
    }
    
    
    public $property;
    public $group;
    public $label;
    public $editor;
    public $editorFormat;
    
    public $position; // position string as understood by positional array sorter

    public $visible = TRUE;
    public $visibleInOverview = TRUE;
    public $visibleInForm = TRUE;

    // only makes sense if editor == SingleSelect
    public $repository;

    // Only makes sense if editor == Radio
    public $options;

    public $readonly = FALSE;
    
    public $readonlyOnEdit = FALSE;
    
    // generic "configuration" block to be used for specific templates
    public $configuration;
    
    public $collectionElement;
    
    public $collectionElementParentProperty;
    
    public $rootModulPath;
    
    public $collectionElementModulPath;
    
    public $collectionElementParentModulPath;
    
    public $collectionElementFormFields;
    
    public $cssClassesFrontEnd;
    
    public $cssClassesBackEnd;

    public $withoutLabel = false;

    public function property(String $property) {
        $this->property = $property;
        return $this;
    }
    public function entityPropertyProperty(String $property) {
        $this->entityPropertyProperty = $property;
        return $this;
    }
    public function label(String $label) {
        $this->label = $label;
        return $this;
    }
    public function visible(String $visible) {
        $this->visible = $visible;
        return $this;
    }
    public function visibleInOverview(String $visibleInOverview) {
        $this->visibleInOverview = $visibleInOverview;
        return $this;
    }
    public function visibleForm(String $visibleForm) {
        $this->visibleForm = $visibleForm;
        return $this;
    }
    public function readonly($readonly) {
        $this->readonly = $readonly;
        return $this;
    }
    public function readonlyOnEdit($readonlyOnEdit) {
        $this->readonlyOnEdit = $readonlyOnEdit;
        return $this;
    }
    public function position(String $position) {
        $this->position = $position;
        return $this;
    }
    public function editor(String $editor) {
        $this->editor = $editor;
        return $this;
    }
    public function editorFormat(String $editorFormat) {
        $this->editorFormat = $editorFormat;
        return $this;
    }
    public function repository(String $repository) {
        $this->repository = $repository;
        return $this;
    }
    public function group(String $group) {
        $this->group = $group;
        return $this;
    }
    public function collectionElement(String $collectionElement) {
        $this->collectionElement = $collectionElement;
        return $this;
    }
    public function collectionElementParentProperty(String $collectionElementParentProperty) {
        $this->collectionElementParentProperty = $collectionElementParentProperty;
        return $this;
    }
    public function rootModulPath(String $rootModulPath) {
        $this->rootModulPath = $rootModulPath;
        return $this;
    }
    public function collectionElementModulPath(String $collectionElementModulPath) {
        $this->collectionElementModulPath = $collectionElementModulPath;
        return $this;
    }
    public function collectionElementParentModulPath(String $collectionElementParentModulPath) {
        $this->collectionElementParentModulPath = $collectionElementParentModulPath;
        return $this;
    }
    public function required($required) {
        $this->required = $required;
        return $this;
    }
    public function cssClassesFrontEnd($cssClassesFrontEnd) {
        $this->cssClassesFrontEnd = $cssClassesFrontEnd;
        return $this;
    }
    public function cssClassesBackEnd($cssClassesBackEnd) {
        $this->cssClassesBackEnd = $cssClassesBackEnd;
        return $this;
    }
    public function withoutLabel($withoutLabel) {
        $this->withoutLabel = $withoutLabel;
        return $this;
    }

}
