<?php

namespace Syntro\SilverstripeBootstrapForms\Tests;

use SilverStripe\Dev\SapphireTest;
use Syntro\SilverstripeBootstrapForms\Forms\TextField;

/**
 * Test the correct handling of the
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class HolderClassTest extends SapphireTest
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
     * testHolderValidationClass
     *
     * @return void
     */
    public function testHolderValidationClass()
    {
        $field = TextField::create('Field', 'Field');

        $field->setMessage('validationError', 'error');

        $this->assertStringContainsString('is-invalid', $field->holderClass());
    }
}
