<?php

namespace Syntro\SilverstripeBootstrapForms\Forms;

/**
 * Adds Bootstrap 5 validation class mapping to form fields
 * @author Jochen Gülden <jochen@guelden.org>
 */
trait BootstrapValidation
{

    /**
     * Maps SilverStripe message types to Bootstrap 5 validation classes
     *
     * SilverStripe message types:
     * - 'error': Validation failures, required field errors
     * - 'validation': Legacy validation messages (treated as errors)
     * - 'required': Required field validation errors
     * - 'good': Success messages
     * - 'warning': Non-critical warnings
     * - 'info': Informational messages
     *
     * Bootstrap 5 validation classes:
     * - 'is-invalid': Red styling for errors
     * - 'is-valid': Green styling for success
     * - '' (empty): No special styling for warnings/info
     *
     * @param string $messageType SilverStripe message type
     * @return string Bootstrap 5 validation class or empty string
     */
    protected function getBootstrapValidationClass($messageType)
    {
        $mapping = [
            'error' => 'is-invalid',      // Validation failures
            'validation' => 'is-invalid', // Legacy validation messages
            'required' => 'is-invalid',   // Required field errors
            'good' => 'is-valid',         // Success messages
            'warning' => '',              // No specific BS5 class
            'info' => '',                 // No specific BS5 class
        ];

        return $mapping[$messageType] ?? '';
    }

    /**
     * Maps SilverStripe message types to Bootstrap 5 feedback classes for message containers
     *
     * Bootstrap 5 feedback classes:
     * - 'invalid-feedback': Red styling for error messages
     * - 'valid-feedback': Green styling for success messages
     * - 'invalid-feedback': Default for other message types (treated as errors)
     *
     * @param string $messageType SilverStripe message type
     * @return string Bootstrap 5 feedback class
     */
    public function getBootstrapFeedbackClass($messageType = null)
    {
        if ($messageType === null) {
            $messageType = $this->getMessageType();
        }

        $mapping = [
            'error' => 'invalid-feedback',      // Validation failures
            'validation' => 'invalid-feedback', // Legacy validation messages
            'required' => 'invalid-feedback',   // Required field errors
            'good' => 'valid-feedback',         // Success messages
            'warning' => 'invalid-feedback',    // Treat warnings as errors for styling
            'info' => 'invalid-feedback',       // Treat info as errors for styling
        ];

        return $mapping[$messageType] ?? 'invalid-feedback';
    }

    /**
     * extraClass - adds Bootstrap validation classes based on message type
     *
     * @return string
     */
    public function extraClass()
    {
        $classes = parent::extraClass();
        $message = $this->getMessage();
        if ($message) {
            $messageType = $this->getMessageType();
            $validationClass = $this->getBootstrapValidationClass($messageType);
            if ($validationClass) {
                $classes .= ' ' . $validationClass;
            }
        }

        return $classes;
    }

    /**
     * Public accessor for Bootstrap validation class - used in templates
     * Returns the validation class for the current field's message state
     *
     * @return string Bootstrap validation class ('is-invalid', 'is-valid', or '')
     */
    public function BootstrapValidationClass()
    {
        $message = $this->getMessage();
        if ($message) {
            $messageType = $this->getMessageType();
            return $this->getBootstrapValidationClass($messageType);
        }
        return '';
    }
}