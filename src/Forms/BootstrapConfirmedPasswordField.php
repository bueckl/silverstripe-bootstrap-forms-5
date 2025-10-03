<?php

namespace Syntro\SilverstripeBootstrapForms\Forms;

use SilverStripe\Forms\ConfirmedPasswordField;
use SilverStripe\Forms\PasswordField;

/**
 * ConfirmedPasswordField with Bootstrap 5 validation styling
 */
class BootstrapConfirmedPasswordField extends ConfirmedPasswordField
{
    use BootstrapValidation;

    /**
     * Override validate method to also set validation messages on child fields
     */
    public function validate($validator)
    {
        $result = parent::validate($validator);
        
        // If validation failed, also set the error on child fields for Bootstrap styling
        if (!$result) {
            $message = $this->getMessage();
            $messageType = $this->getMessageType();
            
            foreach ($this->getChildren() as $child) {
                if ($child instanceof PasswordField) {
                    $child->setMessage($message, $messageType);
                }
            }
            
            // Add the message to the form's main alert area
            if ($this->getForm() && $message) {
                $form = $this->getForm();
                // Get existing form message and append this one
                $existingMessage = $form->getMessage();
                if ($existingMessage) {
                    // Don't duplicate if it's already there
                    if (strpos($existingMessage, $message) === false) {
                        $form->setMessage($existingMessage . '<br>' . $message, $messageType);
                    }
                } else {
                    $form->setMessage($message, $messageType);
                }
            }
            
            // Clear the parent field's message since we've propagated it to children and form
            // This prevents the message from showing twice (once on parent, once on children)
            $this->setMessage('');
        }
        
        return $result;
    }

    /**
     * Override FieldHolder to render child fields directly without parent wrapper
     * This completely bypasses the default FieldHolder template
     */
    public function FieldHolder($properties = array())
    {
        $children = $this->getChildren();
        
        // If parent has a message that wasn't propagated yet (e.g., set after validation),
        // propagate it to children now
        $parentMessage = $this->getMessage();
        if ($parentMessage) {
            $messageType = $this->getMessageType();
            foreach ($children as $child) {
                if ($child instanceof PasswordField && !$child->getMessage()) {
                    $child->setMessage($parentMessage, $messageType);
                }
            }
            
            // Add to form alert if not already there
            if ($this->getForm()) {
                $form = $this->getForm();
                $existingMessage = $form->getMessage();
                if ($existingMessage) {
                    if (strpos($existingMessage, $parentMessage) === false) {
                        $form->setMessage($existingMessage . '<br>' . $parentMessage, $messageType);
                    }
                } else {
                    $form->setMessage($parentMessage, $messageType);
                }
            }
            
            // Clear parent message
            $this->setMessage('');
        }
        
        // Apply validation classes to child fields before rendering
        foreach ($children as $child) {
            if ($child instanceof PasswordField) {
                $this->applyValidationClassesToField($child);
            }
        }
        
        // Render each child with its own FieldHolder
        // This gives us the label, input, and validation message for each field
        $parts = [];
        foreach ($children as $child) {
            $parts[] = $child->FieldHolder();
        }
        
        return implode("\n", $parts);
    }

    /**
     * Apply Bootstrap validation classes to a child field
     */
    private function applyValidationClassesToField($field)
    {
        // Check if the field has a validation message
        $message = $field->getMessage();
        if ($message) {
            $messageType = $field->getMessageType();
            $validationClass = $this->getBootstrapValidationClass($messageType);
            
            if ($validationClass) {
                // Add the validation class to the field
                $field->addExtraClass($validationClass);
            }
        }
    }
}