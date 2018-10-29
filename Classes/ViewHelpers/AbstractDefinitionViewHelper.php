<?php

namespace Sandstorm\CrudForms\ViewHelpers;

use Sandstorm\CrudForms\Annotations\FieldGenerator;
use Sandstorm\CrudForms\Annotations\FormField;
use Sandstorm\CrudForms\Annotations\FormFieldCollectionElement;
use Sandstorm\CrudForms\Annotations\FormFieldGroup;
use Sandstorm\CrudForms\Exception\MissingModelTypeException;
use Sandstorm\CrudForms\FieldGeneratorInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Utility\PositionalArraySorter;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

abstract class AbstractDefinitionViewHelper extends AbstractViewHelper
{

    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @var array
     */
    protected $ignorePropertiesWithAnnotations = [
        Flow\Transient::class,
        Flow\Inject::class,
        Flow\InjectConfiguration::class
    ];

    /**
     * @param string $model
     * @param object $context an arbitrary object which is available in all actions and nested functionality
     * @return array
     * @throws MissingModelTypeException
     * @throws \Exception
     */
    protected function getProperties($model, $context = null)
    {
        if ($model === NULL) {
            throw new MissingModelTypeException('The "model" property has not been specified as parameter to the ViewHelper ' . get_class($this) . '.', 1452715128);
        }
        $propertyNames = $this->reflectionService->getClassPropertyNames($model);
        if ($propertyNames === NULL) {
            throw new MissingModelTypeException('No class schema could be resolved for model ' . $model . '.', 1452715183);
        }

        $fields = [];

        foreach ($propertyNames as $propertyName) {
            if ($propertyName === 'Persistence_Object_Identifier') {
                continue;
            }

            $invalidAnnotations = array_intersect($this->getAnnotationClassNames($model, $propertyName), $this->ignorePropertiesWithAnnotations);
            if (count($invalidAnnotations) > 0) {
                continue;
            }

            /* @var $formFieldAnnotation FormField */
            $formFieldAnnotation = $this->reflectionService->getPropertyAnnotation($model, $propertyName, FormField::class);

            $fields[$propertyName] = [];
            if ($formFieldAnnotation) {
                $fields[$propertyName] = get_object_vars($formFieldAnnotation);
            }
            if (isset($fields[$propertyName]['collectionElement'])) {
                $formFieldAnnotations = $this->reflectionService->getPropertyAnnotations($model, $propertyName, FormFieldCollectionElement::class);
                foreach ($formFieldAnnotations as $annotation) {
                    $annotationArray = get_object_vars($annotation);
                    $fields[$propertyName]['collectionElementFormFields'][$annotationArray['property']] = $annotation;
                }
            }
            
            if (isset($fields[$propertyName]['property'])) {
                $fields[$propertyName]['property'] = $propertyName.".".$fields[$propertyName]['property'];
            }
            $this->addDefaultsToFields($fields, $propertyName);
            
            if ($fields[$propertyName]['editor'] === 'class') {
                $classFieldAnnotation = $this->reflectionService->getPropertyAnnotation($model, $propertyName, 'Doctrine\ORM\Mapping\OneToOne');
                $this->getClassPropertyProperties($classFieldAnnotation->targetEntity, $context, $fields, $propertyName);
                unset($fields[$propertyName]);
            }
        }

        foreach (get_class_methods($model) as $methodName) {
            if (substr($methodName, 0, 3) === 'get') {
                $methodAnnotation = $this->reflectionService->getMethodAnnotation($model, $methodName, FormField::class);

                if ($methodAnnotation) {
                    $propertyName = lcfirst(substr($methodName, 3));
                    $fields[$propertyName] = get_object_vars($methodAnnotation);
                    $this->addDefaultsToFields($fields, $propertyName);
                }
            }
        }
        $generatorAnnotation = $this->reflectionService->getClassAnnotation($model, FieldGenerator::class);
        if ($generatorAnnotation !== NULL) {
            $generator = $this->objectManager->get($generatorAnnotation->className);
            if (!($generator instanceof FieldGeneratorInterface)) {
                throw new \Exception('TODO: generator must implement FieldGeneratorInterface, ' . get_class($generator) . ' given.');
            }
            if ($context != null && method_exists($context ,'setDefaultFields')) {
                $context->setDefaultFields($fields);
            }
            $generatedFields = $generator->generate($context);
            if ($generatedFields != null && !empty($generatedFields) && $context != null && method_exists($context,'getEntityView') && $context->getEntityView() != null) {
                foreach ($fields as $propertyName => $annotation) {
                    $fields[$propertyName]['visible'] = FALSE;
                }
            }
            foreach ($generatedFields as $propertyName => $generatedField) {
                $fields[$propertyName] = get_object_vars($generatedField);
                $this->addDefaultsToFields($fields, $propertyName);
            }
        }
        return (new PositionalArraySorter($fields))->toArray();
    }
    
    /**
     * @param string $model
     * @param object $context an arbitrary object which is available in all actions and nested functionality
     * @param string $fields
     * @param string $classProperty
     * @return void
     * @throws MissingModelTypeException
     * @throws \Exception
     */
    private function getClassPropertyProperties($model, $context = null, &$fields, $classProperty = '')
    {
        if ($model === NULL) {
            throw new MissingModelTypeException('The "model" property has not been specified as parameter to the ViewHelper ' . get_class($this) . '.', 1452715128);
        }
        $propertyNames = $this->reflectionService->getClassPropertyNames($model);
        if ($propertyNames === NULL) {
            throw new MissingModelTypeException('No class schema could be resolved for model ' . $model . '.', 1452715183);
        }
        
        foreach ($propertyNames as $propertyName) {
            if ($propertyName === 'Persistence_Object_Identifier') {
                continue;
            }
            
            $invalidAnnotations = array_intersect($this->getAnnotationClassNames($model, $propertyName), $this->ignorePropertiesWithAnnotations);
            if (count($invalidAnnotations) > 0) {
                continue;
            }
            
            /* @var $formFieldAnnotation FormField */
            $formFieldAnnotation = $this->reflectionService->getPropertyAnnotation($model, $propertyName, FormField::class);
            
            $fields[$classProperty.'.'.$propertyName] = [];
            if ($formFieldAnnotation) {
                $fields[$classProperty.'.'.$propertyName] = get_object_vars($formFieldAnnotation);
            }
            
            if (isset($fields[$classProperty.'.'.$propertyName]['property'])) {
                $fields[$classProperty.'.'.$propertyName]['property'] = $classProperty.'.'.$propertyName.".".$fields[$classProperty.'.'.$propertyName]['property'];
            }
            $this->addDefaultsToFields($fields, $classProperty.'.'.$propertyName);
            if (!isset($fields[$classProperty.'.'.$propertyName]['group'])) {
                $fields[$classProperty.'.'.$propertyName]['group'] = $fields[$classProperty]['group'];
            }
            
            if ($fields[$classProperty.'.'.$propertyName]['editor'] === 'class') {
                $classFieldAnnotation = $this->reflectionService->getPropertyAnnotation($model, $propertyName, 'Doctrine\ORM\Mapping\OneToOne');
                $this->getClassPropertyProperties($classFieldAnnotation->targetEntity, $context, $fields, $classProperty.'.'.$propertyName);
                unset($fields[$classProperty.'.'.$propertyName]);
            }
        }
        
    }
    
    /**
     * @param string $model
     * @param object $context an arbitrary object which is available in all actions and nested functionality
     * @return array
     * @throws MissingModelTypeException
     * @throws \Exception
     */
    protected function getGroups($model, $context = null)
    {
        if ($model === NULL) {
            throw new MissingModelTypeException('The "model" property has not been specified as parameter to the ViewHelper ' . get_class($this) . '.', 1452715128);
        }
        
        /* @var $formFieldAnnotation FormField */
        $formFieldGroupAnnotations = $this->reflectionService->getClassAnnotations($model, FormFieldGroup::class);
        $parentClasses = array();
        $class = $model;
        while ($parent = get_parent_class($class)) {
            $parentClasses[] = $parent;
            $class = $parent;
        }
        
        foreach ($parentClasses as $parentClasse) {
            $formFieldGroupAnnotationParent = $this->reflectionService->getClassAnnotations($parentClasse, FormFieldGroup::class);
            $formFieldGroupAnnotations = array_merge($formFieldGroupAnnotations,$formFieldGroupAnnotationParent);
        }
        
        $groups = [];
        foreach ($formFieldGroupAnnotations as $formFieldGroupAnnotation) {
            $group = $formFieldGroupAnnotation->group;
            $groups[$group] = get_object_vars($formFieldGroupAnnotation);
            $this->addDefaultsToGroups($groups, $group);
        }

        $generatorAnnotation = $this->reflectionService->getClassAnnotation($model, FieldGenerator::class);
        if ($generatorAnnotation !== NULL) {
            $generator = $this->objectManager->get($generatorAnnotation->className);
            if (!($generator instanceof FieldGeneratorInterface)) {
                throw new \Exception('TODO: generator must implement FieldGeneratorInterface, ' . get_class($generator) . ' given.');
            }
            $generatedGroups = $generator->groups($context);
            if ($generatedGroups != null && !empty($generatedGroups) && $context != null && method_exists($context,'getEntityView') && $context->getEntityView() != null) {
                $groups = [];
            }
            foreach ($generatedGroups as $generatedGroup => $annotation) {
                $group = $annotation->group;
                $groups[$group] = get_object_vars($annotation);
                $this->addDefaultsToGroups($groups, $group);
            }
        }
        
        return (new PositionalArraySorter($groups))->toArray();
    }
    
    private function getAnnotationClassNames($model, $property)
    {
        $annotations = $this->reflectionService->getPropertyAnnotations($model, $property);
        return array_keys($annotations);
    }

    private function addDefaultsToFields(&$fields, $propertyName)
    {
         if (!isset($fields[$propertyName]['property'])) {
             $fields[$propertyName]['property'] = $propertyName;
         }
        
        if (!isset($fields[$propertyName]['label'])) {
            $fields[$propertyName]['label'] = $propertyName;
        }

        if (!array_key_exists('visible', $fields[$propertyName])) {
            $fields[$propertyName]['visible'] = TRUE;
        }
        
        if (!array_key_exists('readonlyOnEdit', $fields[$propertyName])) {
            $fields[$propertyName]['readonlyOnEdit'] = FALSE;
        }
        
        if (!array_key_exists('visibleInOverview', $fields[$propertyName])) {
            $fields[$propertyName]['visibleInOverview'] = TRUE;
        }

        if (!array_key_exists('visibleInForm', $fields[$propertyName])) {
            $fields[$propertyName]['visibleInForm'] = TRUE;
        }
        if (!array_key_exists('editor', $fields[$propertyName])) {
            $fields[$propertyName]['editor'] = '';
        }
    }
    private function addDefaultsToGroups(&$groups, $groupName)
    {
        if (!isset($groups[$groupName]['group'])) {
            $groups[$groupName]['group'] = $propertyName;
        }
        
        if (!isset($groups[$groupName]['label'])) {
            $groups[$groupName]['label'] = $groupName;
        }
        
        if (!array_key_exists('visible', $groups[$groupName])) {
            $groups[$groupName]['visible'] = TRUE;
        }
        
        if (!array_key_exists('visibleInOverview', $groups[$groupName])) {
            $groups[$groupName]['visibleInOverview'] = TRUE;
        }
        
        if (!array_key_exists('visibleInForm', $groups[$groupName])) {
            $groups[$groupName]['visibleInForm'] = TRUE;
        }
    }
}
