<?php

namespace Syntro\SilverstripeBootstrapForms\Tests;

use SilverStripe\Dev\SapphireTest;
use Syntro\SilverstripeBootstrapForms\Forms\TextField;

/**
 * Test the correct handling of the Bootstrap validation classes
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class BootstrapValidationTest extends SapphireTest
{
    protected static $fixture_file = './fixture.yml';

    /**
     * test no title
     *
     * @return void
     */
    public function testNoTitleNoLabel()
    {
        $field = TextField::create('Field', 'Field');
        $field->setTitle(null);
        $this->assertEquals('form-holder--no-label', $field->holderClass());
    }

    /**
     * testCheckHolderClass
     *
     * @return void
     */
    public function testCheckHolderClass()
    {
        $field = TextField::create('Field', 'Field');

        $field->addHolderClass('holderclass');

        $this->assertFalse($field->hasHolderClass('test'));

        $this->assertTrue($field->hasHolderClass('holderclass'));
    }


    /**
     * testHolderClassManipulation
     *
     * @return void
     */
    public function testHolderClassManipulation()
    {
        $field = TextField::create('Field', 'Field');

        $field->addHolderClass('testholderclass');
        $this->assertEquals('testholderclass', $field->holderClass());

        $field->addHolderClass('secondclass');
        $this->assertEquals('testholderclass secondclass', $field->holderClass());

        $field->removeHolderClass('testholderclass');
        $this->assertEquals('secondclass', $field->holderClass());

        $field->removeHolderClass('secondclass');
        $this->assertEquals('', $field->holderClass());

    }

    /**
     * testValidationClass
     *
     * @return void
     */
    public function testValidationClass()
    {
        $field = TextField::create('Field', 'Field');

        $field->setMessage('validationError', 'error');

        $this->assertStringContainsString('is-invalid', $field->extraClass());
    }

    /**
     * testLegacyValidationMessageType
     *
     * @return void
     */
    public function testLegacyValidationMessageType()
    {
        $field = TextField::create('Field', 'Field');

        $field->setMessage('validationError', 'validation');

        $this->assertStringContainsString('is-invalid', $field->extraClass());
    }

    /**
     * testRequiredMessageType
     *
     * @return void
     */
    public function testRequiredMessageType()
    {
        $field = TextField::create('Field', 'Field');

        $field->setMessage('This field is required', 'required');

        $this->assertStringContainsString('is-invalid', $field->extraClass());
    }

    /**
     * testHolderValidationClass
     *
     * @return void
     */
    public function testHolderValidationClass()
    {
        $field = TextField::create('Field', 'Field');

        $field->setMessage('validationError', 'error');

        // Holder should NOT get validation classes - only the input field does
        $this->assertStringNotContainsString('is-invalid', $field->holderClass());
        $this->assertStringNotContainsString('is-valid', $field->holderClass());
    }

    /**
     * testValidClass
     *
     * @return void
     */
    public function testValidClass()
    {
        $field = TextField::create('Field', 'Field');

        $field->setMessage('success message', 'good');

        $this->assertStringContainsString('is-valid', $field->extraClass());
        // Holder should NOT get validation classes
        $this->assertStringNotContainsString('is-valid', $field->holderClass());
    }

    /**
     * testWarningAndInfoClasses
     *
     * @return void
     */
    public function testWarningAndInfoClasses()
    {
        $field = TextField::create('Field', 'Field');

        $field->setMessage('warning message', 'warning');
        $this->assertStringNotContainsString('is-invalid', $field->extraClass());
        $this->assertStringNotContainsString('is-valid', $field->extraClass());
        $this->assertStringNotContainsString('is-invalid', $field->holderClass());
        $this->assertStringNotContainsString('is-valid', $field->holderClass());

        $field->setMessage('info message', 'info');
        $this->assertStringNotContainsString('is-invalid', $field->extraClass());
        $this->assertStringNotContainsString('is-valid', $field->extraClass());
        $this->assertStringNotContainsString('is-invalid', $field->holderClass());
        $this->assertStringNotContainsString('is-valid', $field->holderClass());
    }

    /**
     * testBootstrapValidationClassMapping
     *
     * @return void
     */
    public function testBootstrapValidationClassMapping()
    {
        $field = TextField::create('Field', 'Field');

        // Test all SilverStripe message types
        $testCases = [
            'error' => 'is-invalid',
            'validation' => 'is-invalid',
            'required' => 'is-invalid',
            'good' => 'is-valid',
            'warning' => '',
            'info' => '',
            'unknown' => '',
        ];

        foreach ($testCases as $messageType => $expectedClass) {
            $field->setMessage('test message', $messageType);
            $extraClass = $field->extraClass();

            if ($expectedClass) {
                $this->assertStringContainsString($expectedClass, $extraClass,
                    "Message type '$messageType' should contain '$expectedClass'");
            } else {
                $this->assertStringNotContainsString('is-invalid', $extraClass,
                    "Message type '$messageType' should not contain validation classes");
                $this->assertStringNotContainsString('is-valid', $extraClass,
                    "Message type '$messageType' should not contain validation classes");
            }
        }
    }
}
