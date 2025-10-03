<?php

namespace Syntro\SilverstripeBootstrapForms\Forms;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\OptionsetField as BackendOptionsetField;

/**
 * Frontend OptionsetField
 * @author Matthias LEutenegger <hello@syntro.ch>
 */
class OptionsetField extends BackendOptionsetField
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
        // $this->setFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\FormField_holder');
        // $this->setSmallFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\FormField_holder_small');
        $this->setupDefaultHolderClasses();
        parent::__construct($name, $title, $source, $value);
    }
}
