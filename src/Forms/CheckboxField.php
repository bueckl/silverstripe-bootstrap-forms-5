<?php

namespace Syntro\SilverstripeBootstrapForms\Forms;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\CheckboxField as BackendCheckboxField;

/**
 * Frontend CheckboxField
 *
 * @author Matthias LEutenegger <hello@syntro.ch>
 */
class CheckboxField extends BackendCheckboxField
{

    use HolderClass, BootstrapValidation;

    /**
     * Ensures that the methods are wrapped in the correct type and
     * values are safely escaped while rendering in the template.
     * @config
     * @var array
     */
    private static $casting = [
        'Title' => 'HTMLText'
    ];

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
        // $this->setFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\FormField_holder');
        // $this->setSmallFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\FormField_holder_small');
        $this->setupDefaultHolderClasses();
        parent::__construct($name, $title, $value);
    }
}
