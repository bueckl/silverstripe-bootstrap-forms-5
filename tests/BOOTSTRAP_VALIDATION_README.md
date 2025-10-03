# Bootstrap 5 Validation System

A clean, trait-based system for adding Bootstrap 5 validation styling to SilverStripe form fields.

## Overview

The `BootstrapValidation` trait automatically maps SilverStripe form validation message types to Bootstrap 5 CSS classes, providing proper visual feedback for form validation states.

## Message Container Classes

The system also applies correct Bootstrap 5 feedback classes to message containers:

| SilverStripe Message Type | Bootstrap 5 Feedback Class |
|--------------------------|---------------------------|
| `error` | `invalid-feedback` |
| `validation` | `invalid-feedback` |
| `required` | `invalid-feedback` |
| `good` | `valid-feedback` |
| `warning` | `invalid-feedback` |
| `info` | `invalid-feedback` |

Message containers automatically get the appropriate Bootstrap 5 feedback classes for consistent styling.

## Usage

### With Syntro Bootstrap Forms (Recommended)

All Syntro form fields already include Bootstrap validation:

```php
use Syntro\SilverstripeBootstrapForms\Forms\TextField;

$field = TextField::create('Name', 'Your Name');
// Automatically includes Bootstrap validation styling
```

### With Standard SilverStripe Fields

Apply the trait to any SilverStripe form field:

```php
use SilverStripe\Forms\TextField;
use Syntro\SilverstripeBootstrapForms\Forms\BootstrapValidation;

class MyTextField extends TextField {
    use BootstrapValidation;
}

// Usage
$field = MyTextField::create('Name', 'Your Name');
```

### Custom Form Fields

Create custom fields with Bootstrap validation:

```php
<?php

namespace App\Forms;

use SilverStripe\Forms\TextField;
use Syntro\SilverstripeBootstrapForms\Forms\BootstrapValidation;

class BootstrapTextField extends TextField
{
    use BootstrapValidation;

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength, $form);
        // Add any custom Bootstrap classes
        $this->addExtraClass('form-control-lg');
    }
}
```

## Form Integration

Use validated fields in your forms:

```php
<?php

namespace App\Forms;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use App\Forms\BootstrapTextField;

class ContactForm extends Form
{
    public function __construct($controller, $name)
    {
        $fields = FieldList::create(
            BootstrapTextField::create('Name', 'Your Name'),
            BootstrapTextField::create('Email', 'Email Address'),
            BootstrapTextField::create('Message', 'Your Message')
        );

        $actions = FieldList::create(
            FormAction::create('submitForm', 'Send Message')
                ->addExtraClass('btn btn-primary')
        );

        parent::__construct($controller, $name, $fields, $actions);

        // Add validation
        $this->setValidator(RequiredFields::create(['Name', 'Email', 'Message']));
    }

    public function submitForm($data, $form)
    {
        // Validation errors will display with Bootstrap 5 styling
        // (red borders for errors, green for success)
    }
}
```

## Template Requirements

Ensure your form templates support Bootstrap 5 validation feedback:

```html
<div class="mb-3">
    $Field
    <% if $Message %>
        <div class="invalid-feedback d-block">
            $Message
        </div>
    <% end_if %>
</div>
```

With Bootstrap validation applied, you'll get properly styled output:

```html
<!-- Input field with validation -->
<input class="form-control is-invalid" ...>

<!-- Message container with correct Bootstrap class -->
<div class="message invalid-feedback">
    Please enter an email address<br>
    Please provide a valid email address
</div>
```

## How It Works

The `BootstrapValidation` trait overrides the `extraClass()` method to:

1. Call the parent `extraClass()` method
2. Check if the field has a validation message
3. Map the SilverStripe message type to Bootstrap 5 classes
4. Append the appropriate Bootstrap class to the field's CSS classes

## Compatibility

- **SilverStripe**: 5.0+
- **Bootstrap**: 5.0+
- **PHP**: 8.1+

## Testing

Test validation styling with different message types:

```php
$field = BootstrapTextField::create('Test');

// Test error state
$field->setMessage('This field is required', 'required');
// $field->extraClass() now includes 'is-invalid'

// Test success state
$field->setMessage('Field is valid', 'good');
// $field->extraClass() now includes 'is-valid'
```

## Architecture

- **Trait-based**: Clean separation of concerns
- **Non-intrusive**: Doesn't modify existing field behavior
- **Composable**: Can be combined with other traits
- **Standard**: Uses SilverStripe's built-in message system

---

## Advanced: ConfirmedPasswordField Integration

The `BootstrapConfirmedPasswordField` extends SilverStripe's `ConfirmedPasswordField` with Bootstrap 5 validation styling. This field handles special cases for composite password fields.

### Features

1. **Child Field Validation**: Validation messages are propagated to both child password fields
2. **Bootstrap Styling**: Both password inputs get `is-invalid` class when validation fails
3. **Clean Rendering**: No duplicate messages or unnecessary wrapper divs
4. **Form-level Messages**: Validation errors appear in the form's alert area

### Implementation

```php
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
     * Override validate method to propagate messages to child fields
     * and add to form's main alert area
     */
    public function validate($validator)
    {
        $result = parent::validate($validator);
        
        // If validation failed, propagate error to child fields
        if (!$result) {
            $message = $this->getMessage();
            $messageType = $this->getMessageType();
            
            // Set message on both child password fields
            foreach ($this->getChildren() as $child) {
                if ($child instanceof PasswordField) {
                    $child->setMessage($message, $messageType);
                }
            }
            
            // Add message to form's main alert area
            if ($this->getForm() && $message) {
                $form = $this->getForm();
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
            
            // Clear parent message to prevent duplication
            $this->setMessage('');
        }
        
        return $result;
    }

    /**
     * Override FieldHolder to render child fields directly
     * Bypasses default template that adds middleColumn wrapper
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
        
        // Apply validation classes to child fields
        foreach ($children as $child) {
            if ($child instanceof PasswordField) {
                $this->applyValidationClassesToField($child);
            }
        }
        
        // Render each child with its own FieldHolder
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
        $message = $field->getMessage();
        if ($message) {
            $messageType = $field->getMessageType();
            $validationClass = $this->getBootstrapValidationClass($messageType);
            
            if ($validationClass) {
                $field->addExtraClass($validationClass);
            }
        }
    }
}
```

### Usage in Forms

```php
use Syntro\SilverstripeBootstrapForms\Forms\BootstrapConfirmedPasswordField;

$fields = FieldList::create(
    TextField::create('Email', 'Email Address'),
    BootstrapConfirmedPasswordField::create('Password', 'Enter a password')
);

$validator = RequiredFields::create(['Email', 'Password']);
```

### HTML Output

When validation fails, the field renders as:

```html
<!-- Child password field 1 -->
<div class="mb-3 col-12 col-md-6">
  <label for="Form_Password_Password" class="form-label">Enter a password</label>
  <input type="password" 
         name="Password[_Password]" 
         class="text password form-control is-invalid" 
         id="Form_Password_Password">
  <div class="invalid-feedback">Passwords can't be empty</div>
</div>

<!-- Child password field 2 -->
<div class="mb-3 col-12 col-md-6">
  <label for="Form_Password_ConfirmPassword" class="form-label">Re-enter password</label>
  <input type="password" 
         name="Password[_ConfirmPassword]" 
         class="text password form-control is-invalid" 
         id="Form_Password_ConfirmPassword">
  <div class="invalid-feedback">Passwords can't be empty</div>
</div>
```

**Note**: No `middleColumn` wrapper div, no duplicate parent messages - just clean Bootstrap 5 markup.

### Form Alert Integration

The validation message also appears in the form's alert area at the top:

```html
<div class="alert alert-danger">
    <p class="message validation">Passwords can't be empty</p>
</div>
```

### Key Benefits

1. **Consistent UX**: Users see validation errors both inline (on fields) and in summary (form alert)
2. **Clean Markup**: No unnecessary wrapper divs that break Bootstrap grid layouts
3. **Accessibility**: Each field has its own label and validation message
4. **No Duplication**: Parent field message is cleared after propagating to children and form
5. **Flexible Message Handling**: Messages set during validation OR after validation (in callbacks) are properly propagated
6. **Event Delegation**: AJAX forms continue to work after form HTML is replaced via AJAX updates

### Template Customization

The field uses standard Bootstrap field templates. To customize individual password fields:

```silverstripe
<!-- In your FieldHolder template -->
<div class="mb-3 $HolderClasses">
  <label for="$ID" class="form-label">$Title</label>
  $Field
  <% if $Message %>
    <div class="$MessageFeedbackClass">$Message</div>
  <% end_if %>
</div>
```

Where `$MessageFeedbackClass` comes from the `BootstrapValidation` trait's `getBootstrapFeedbackClass()` method.

### AJAX Form Integration

When used with AJAX forms, the BootstrapConfirmedPasswordField works seamlessly:

```php
// In your form's validation response callback
$this->setValidationResponseCallback(function($result) {
    $request = $this->getController()->getRequest();
    
    if ($request && $request->isAjax()) {
        // Transfer ValidationResult errors to form fields
        // This is crucial for displaying field-level errors
        if ($result && !$result->isValid()) {
            foreach ($result->getMessages() as $error) {
                $fieldName = $error['fieldName'] ?? null;
                $message = $error['message'] ?? null;
                $messageType = $error['messageType'] ?? 'error';
                
                if ($fieldName && $message) {
                    $field = $this->Fields()->dataFieldByName($fieldName);
                    if ($field) {
                        $field->setMessage($message, $messageType);
                        // BootstrapConfirmedPasswordField will propagate this
                        // to child fields when rendering
                    }
                }
            }
        }
        
        // Also populate any errors from session
        $this->setupFormErrors();
        
        return HTTPResponse::create(json_encode([
            'valid' => false,
            'html' => (string) $this->forTemplate() // Re-render form with errors
        ]))->addHeader('Content-type', 'application/json');
    }
    
    return null;
});
```

**Key Points:**

1. **Extract errors from ValidationResult**: The `$result` parameter contains all field errors from your `validate()` method
2. **Set messages on fields**: Transfer errors from ValidationResult to actual FormField objects
3. **BootstrapConfirmedPasswordField handles propagation**: When a message is set on the parent field, the `FieldHolder()` method automatically propagates it to child password fields
4. **Render after setting messages**: Call `forTemplate()` after all messages are set

The JavaScript can then update the form holder with validation errors properly styled:

```javascript
$.ajax({
    // ... ajax config
    success: function(data) {
        if (data.valid) {
            // Success - show thank you message
            holder.html(data.html);
        } else {
            // Validation errors - update form with error styling
            holder.html(data.html);
            
            // Focus first invalid field
            holder.find('.is-invalid:first').trigger('focus');
        }
    }
});
```

### Common Validation Scenarios

**Empty Passwords**:
```
Message: "Passwords can't be empty"
Type: validation
Bootstrap Classes: is-invalid, invalid-feedback
```

**Password Mismatch**:
```
Message: "Passwords don't match"
Type: validation  
Bootstrap Classes: is-invalid, invalid-feedback
```

**Password Too Short**:
```
Message: "Password must be at least 8 characters"
Type: validation
Bootstrap Classes: is-invalid, invalid-feedback
```

**Success**:
```
No message displayed
Bootstrap Classes: (none - validation removed on success)
```

---

## Custom Form Validation with AJAX

When implementing custom validation logic in your form (e.g., checking for duplicate emails), you need to ensure errors are properly transferred from the `ValidationResult` to the form fields for display.

### Complete Example: RegisterForm with Custom Validation

```php
<?php

namespace App\Forms;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Control\HTTPResponse;
use Syntro\SilverstripeBootstrapForms\Forms\TextField;
use Syntro\SilverstripeBootstrapForms\Forms\EmailField;
use Syntro\SilverstripeBootstrapForms\Forms\BootstrapConfirmedPasswordField;

class RegisterForm extends Form
{
    public function __construct($controller, $name)
    {
        $fields = FieldList::create(
            TextField::create('FirstName', 'First name'),
            TextField::create('Surname', 'Last name'),
            EmailField::create('Email', 'Email address'),
            BootstrapConfirmedPasswordField::create('Password', 'Password')
        );

        $actions = FieldList::create(
            FormAction::create('register', 'Submit')
                ->addExtraClass('btn btn-primary')
        );

        $validator = RequiredFields::create([
            'FirstName',
            'Surname', 
            'Email',
            'Password'
        ]);

        parent::__construct($controller, $name, $fields, $actions, $validator);

        // Set custom validation response callback for AJAX requests
        $this->setValidationResponseCallback(function($result) {
            $request = $this->getController()->getRequest();
            
            // Check if this is an AJAX request
            if ($request && $request->isAjax()) {
                
                // Transfer ValidationResult errors to form fields
                // This is crucial for displaying field-level errors
                if ($result && !$result->isValid()) {
                    foreach ($result->getMessages() as $error) {
                        $fieldName = $error['fieldName'] ?? null;
                        $message = $error['message'] ?? null;
                        $messageType = $error['messageType'] ?? 'error';
                        
                        if ($fieldName && $message) {
                            $field = $this->Fields()->dataFieldByName($fieldName);
                            if ($field) {
                                $field->setMessage($message, $messageType);
                            }
                        }
                    }
                }
                
                // Also populate any errors from session
                $this->setupFormErrors();
                
                $ajaxData = [
                    'valid' => false,
                    'msg' => "You've got errors in your submission. Please correct these.",
                    'html' => (string) $this->forTemplate()
                ];
                
                $response = new HTTPResponse(json_encode($ajaxData));
                $response->addHeader('Content-Type', 'application/json');
                return $response;
            }
            
            // Return null to use default behavior for non-AJAX requests
            return null;
        });
    }

    /**
     * Custom validation - check for duplicate email
     */
    public function validate(): ValidationResult 
    {
        // First run the parent's validation (RequiredFields, etc.)
        $result = parent::validate();
        
        $data = $this->getData();

        // Check for duplicate email
        if (!empty($data['Email']) && filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
            $existing = Member::get()->filter('Email:nocase', $data['Email']);
            if ($current = Security::getCurrentUser()) {
                $existing = $existing->filter('ID:not', $current->ID);
            }
            if ($existing->exists()) {
                // Add field error - will be picked up by validation callback
                $result->addFieldError(
                    'Email', 
                    'There is already a user with the email ' . $data['Email'],
                    ValidationResult::TYPE_ERROR
                );
            }
        }

        return $result;
    }

    /**
     * Form action - handles successful submission
     */
    public function register($data, Form $form)
    {
        // If we reach here, validation has passed
        
        // ... save member, send emails, etc.
        
        $ajaxData = [
            'valid' => true,
            'html' => '<h2>Thank you!</h2><p>Registration successful.</p>'
        ];

        $response = new HTTPResponse(json_encode($ajaxData));
        $response->addHeader('Content-type', 'application/json');
        return $response;
    }
}
```

### JavaScript Integration with Event Delegation

For AJAX forms that update their HTML after submission, use event delegation to ensure handlers persist:

```javascript
var RegisterForm = function(form, modal, formContainer) {
    var $this = this;
    
    this.init = function(){
        $this.bind_submitclick();
        $this.bind_formsubmit();
    }

    /**
     * Prevent default form submission - we handle it via AJAX
     * Use event delegation on formContainer (which doesn't get replaced)
     */
    this.bind_formsubmit = function(){
        formContainer.off('submit.register').on('submit.register', 'form', function(e) {
            console.log('Form submit event triggered - preventing default');
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    }

    /**
     * Binding submit button click with event delegation
     */
    this.bind_submitclick = function(){
        console.log('Setting up delegated click handler on formContainer');
        
        // Use event delegation - attach to formContainer which doesn't get replaced
        formContainer.off('click.register').on('click.register', '[name=action_register]', function(e) {
            console.log('Button clicked, preventing default submission');
            e.preventDefault();
            e.stopPropagation();
            
            // Get current form (might be new after AJAX update)
            var $form = formContainer.find('form');
            var button = $(this);
            
            button.button && button.button('loading');

            var action = $form.attr('action');
            var payload = $form.serialize();

            var handleResponse = function(raw){
                console.log(raw);
                button.button && button.button('reset');

                if (raw.valid) {
                    // Success - show thank you message
                    var holder = $form.parent();
                    holder.html(raw.html);
                } else {
                    // Validation errors - update form with error styling
                    var holder = $form.parent();
                    holder.html(raw.html);
                    
                    // Focus first invalid field for accessibility
                    setTimeout(function(){
                        var firstInvalid = holder.find('.is-invalid:first, [aria-invalid="true"]:first');
                        if (firstInvalid.length) {
                            firstInvalid.trigger('focus');
                        }
                    }, 30);

                    // Scroll to form
                    $("html, body").animate({
                        scrollTop: holder.offset().top - 300
                    }, 500);
                }
            };

            console.log('About to make AJAX request');

            // Use jQuery AJAX
            $.ajax({
                url: action,
                type: 'POST',
                data: payload,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    console.log('AJAX success - received data:', data);
                    handleResponse(data);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    button.button && button.button('reset');
                }
            });
            return false;
        });
    }

    // Automatic initialization
    $this.init();
}
```

**Key JavaScript Points:**

1. **Event Delegation**: Bind to `formContainer` (parent that doesn't change), not the form itself
2. **Dynamic Form Reference**: Get the current form element each time (`formContainer.find('form')`)
3. **Persist After Updates**: Event handlers remain active even after form HTML is replaced
4. **Proper Error Handling**: Focus first invalid field, scroll to form, provide user feedback

### Validation Flow Diagram

```
User submits form
    ↓
JavaScript prevents default, makes AJAX request
    ↓
PHP: Form->validate() is called
    ↓
PHP: Custom validate() method runs (e.g., duplicate email check)
    ↓
PHP: Returns ValidationResult with field errors
    ↓
PHP: ValidationResponseCallback is triggered
    ↓
PHP: Callback transfers errors from ValidationResult to FormFields
    ↓
PHP: BootstrapConfirmedPasswordField->FieldHolder() propagates messages to children
    ↓
PHP: Form is rendered with error messages and Bootstrap classes
    ↓
PHP: Returns JSON {valid: false, html: "...rendered form..."}
    ↓
JavaScript: Updates form holder with new HTML
    ↓
JavaScript: Focuses first invalid field
    ↓
User sees validation errors with Bootstrap styling
```

### Troubleshooting

**Problem**: Validation errors not showing on fields after AJAX submission

**Solution**: Ensure your validation response callback transfers errors from `ValidationResult` to fields:
```php
foreach ($result->getMessages() as $error) {
    $field = $this->Fields()->dataFieldByName($error['fieldName']);
    if ($field) {
        $field->setMessage($error['message'], $error['messageType']);
    }
}
```

**Problem**: Form stops working after second AJAX submission

**Solution**: Use event delegation instead of direct binding:
```javascript
// Wrong - binds to specific element
button.on('click', handler);

// Right - uses delegation on parent
formContainer.on('click', '[name=action_register]', handler);
```

**Problem**: ConfirmedPasswordField errors not showing on child fields

**Solution**: The `BootstrapConfirmedPasswordField->FieldHolder()` method now automatically propagates parent messages to children before rendering, even if messages are set after validation.

---
