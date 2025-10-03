<?php

namespace Syntro\SilverstripeBootstrapForms\Forms;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\TextareaField as BackendTextareaField;

/**
 * Frontend TextareaField
 * @author Matthias LEutenegger <hello@syntro.ch>
 */
class TextareaField extends BackendTextareaField
{

    use HolderClass, BootstrapValidation;


    /**
     * Creates a new field.
     *
     * @param string                                      $name  The internal field name, passed to forms.
     * @param null|string|\SilverStripe\View\ViewableData $title The human-readable field label.
     * @param mixed                                       $value The value of the field.
     */
    function __construct($name, $title = null, $value = null)
    {
        // $this->setTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\TextField');
        $this->setFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\FormField_holder');
        $this->setSmallFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\FormField_holder_small');
        $this->setupDefaultHolderClasses();
        parent::__construct($name, $title, $value);
    }
}
