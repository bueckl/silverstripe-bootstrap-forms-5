<?php

namespace Syntro\SilverstripeBootstrapForms\Forms;

/**
 * Adds the capability to modify a class on the holder
 * @author Matthias Leutenegger
 */
trait HolderClass
{

    /**
     * Stores the css-classes for the field holder
     * @var array|null
     */
    protected $holderClasses;

    /**
     * @config
     * @var array $default_holder_classes The default classes to apply to the FormField
     */
    private static $default_holder_classes = [];

    /**
     * Compiles all CSS-classes. Optionally includes a "form-group--no-label" class if no title was set on the
     * FormField.
     *
     * Uses {@link Message()} and {@link MessageType()} to add validation error classes which can
     * be used to style the contained tags.
     *
     * @return string
     */
    public function holderClass()
    {
        $classes = [];

        if ($this->holderClasses) {
            $classes = array_merge(
                $classes,
                array_values($this->holderClasses ?? [])
            );
        }

        if (!$this->Title()) {
            $classes[] = 'form-holder--no-label';
        }

        // Add is-invalid class to holder when validation fails
        $message = $this->getMessage();
        if ($message && $this->getMessageType() == 'error') {
            $classes[] = 'is-invalid';
        }

        return implode(' ', $classes);
    }

    /**
     * Set up the default classes for the form. This is done on construct so that the default classes can be removed
     * after instantiation
     * @return void
     */
    protected function setupDefaultHolderClasses()
    {
        $defaultClasses = $this->config()->get('default_holder_classes');
        if ($defaultClasses) {
            foreach ($defaultClasses as $class) {
                $this->addHolderClass($class);
            }
        }
    }

    /**
     * Check if a CSS-class has been added to the field holder.
     *
     * @param string $class A string containing a classname or several class
     *                      names delimited by a single space.
     * @return boolean True if all of the classnames passed in have been added.
     */
    public function hasHolderClass($class)
    {
        //split at white space
        $classes = preg_split('/\s+/', $class);
        foreach ($classes as $class) {
            if (!isset($this->holderClasses[$class])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add one or more CSS-classes to the FormField holder.
     *
     * Multiple class names should be space delimited.
     *
     * @param string $class the class or classes to add
     *
     * @return $this
     */
    public function addHolderClass($class)
    {
        $classes = preg_split('/\s+/', $class);

        foreach ($classes as $class) {
            $this->holderClasses[$class] = $class;
        }

        return $this;
    }

    /**
     * Remove one or more CSS-classes from the FormField holder.
     *
     * @param string $class the class or classes to remove
     *
     * @return $this
     */
    public function removeHolderClass($class)
    {
        $classes = preg_split('/\s+/', $class);

        foreach ($classes as $class) {
            unset($this->holderClasses[$class]);
        }

        return $this;
    }

    /**
     * extraClass - adds the default bootstrap invalid class if a validation error occurs
     *
     * @return string
     */
    public function extraClass()
    {
        $classes = parent::extraClass();
        $message = $this->getMessage();
        if ($message && $this->getMessageType() == 'error') {
            $classes .= ' is-invalid';
        }

        return $classes;
    }
}
