<?php

namespace Syntro\SilverstripeBootstrapForms\Forms;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\CheckboxSetField as BackendCheckboxSetField;

/**
 * Frontend CheckboxSetField
 * @author Matthias LEutenegger <hello@syntro.ch>
 */
class CheckboxSetField extends BackendCheckboxSetField
{

    use HolderClass, BootstrapValidation;

    /**
     * Returns an input field.
     *
     * @param string             $name   The field name
     * @param string             $title  The field title
     * @param array|\ArrayAccess $source A map of the dropdown items
     * @param mixed              $value  The current value
     */
    function __construct($name, $title = null, $source = [], $value = null)
    {
        $this->setFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\OptionsetField_holder');
        $this->setSmallFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\OptionsetField_holder_small');
        $this->setupDefaultHolderClasses();
        parent::__construct($name, $title, $source, $value);
    }
}
