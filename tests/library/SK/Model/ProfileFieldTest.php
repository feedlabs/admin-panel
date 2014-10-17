<?php

class SK_Model_ProfileFieldTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testGetValues() {
        $field = SK_Model_ProfileField::create('foo', 'select', array(2, 4));
        $this->assertEquals(array(2, 4), $field->getValues());

        $textField = SK_Model_ProfileField::create('text', SK_Model_ProfileField::PRESENTATION_TEXT, null);
        try {
            $textField->getValues();
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Cannot access values for `text` field.', $ex->getMessage());
        }

        $textareaField = SK_Model_ProfileField::create('textarea', SK_Model_ProfileField::PRESENTATION_TEXTAREA, null);
        try {
            $textareaField->getValues();
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Cannot access values for `textarea` field.', $ex->getMessage());
        }

        $ageRangeField = SK_Model_ProfileField::create('ageRange', SK_Model_ProfileField::PRESENTATION_AGE_RANGE, null);
        try {
            $ageRangeField->getValues();
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Cannot access values for `age_range` field.', $ex->getMessage());
        }
    }

    public function testCreate() {
        try {
            SK_Model_ProfileField::create('bar', SK_Model_ProfileField::PRESENTATION_SELECT, null);
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Missing values array for `select` field.', $ex->getMessage());
        }

        try {
            SK_Model_ProfileField::create('foo', SK_Model_ProfileField::PRESENTATION_TEXT, array(2, 4));
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Cannot set values for `text` field.', $ex->getMessage());
        }
    }
}
