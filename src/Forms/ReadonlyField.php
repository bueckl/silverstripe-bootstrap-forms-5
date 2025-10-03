<?php

namespace Syntro\SilverstripeBootstrapForms\Forms;

use SilverStripe\Forms\ReadonlyField as BackendReadonlyField;

/**
 * Frontend ReadonlyField
 *
 * @author Patrick Côté <patrick.cote@projektneptun.ch>
 */
class ReadonlyField extends BackendReadonlyField
{
    use HolderClass, BootstrapValidation;

  /**
   * Returns a readonly field.
   *
   * @param string      $name  name of the field
   * @param null|string $title title of the field
   * @param null|string $value value of the field
   *
   *
   */
    function __construct($name, $title = null, $value = null)
    {
        $this->setFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\FormField_holder');
        $this->setSmallFieldHolderTemplate('Syntro\\SilverstripeBootstrapForms\\Forms\\FormField_holder_small');
        $this->setupDefaultHolderClasses();
        parent::__construct($name, $title, $value);
    }
}
