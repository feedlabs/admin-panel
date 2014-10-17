<?php

class SK_FormField_AgeRangeTest extends SKTest_TestCase {

    public function testValidate() {
        $field = new SK_FormField_AgeRange();
        $environment = new CM_Frontend_Environment();
        $userInput = array('18', '20');
        $validationExpected = array(18, 20);
        $validationResult = $field->validate($environment, $userInput);

        $this->assertEquals($validationExpected, $validationResult);

        $userInput = array('25', '18');
        $validationExpected = array(18, 25);
        $validationResult = $field->validate($environment, $userInput);

        $this->assertEquals($validationExpected, $validationResult);

        $userInput = array('', '18');
        $validationExpected = array(null, 18);
        $validationResult = $field->validate($environment, $userInput);

        $this->assertEquals($validationExpected, $validationResult);

        $userInput = array('18', '');
        $validationExpected = array(18, null);
        $validationResult = $field->validate($environment, $userInput);

        $this->assertEquals($validationExpected, $validationResult);

        $userInput = array('', '');
        $validationExpected = array(null, null);
        $validationResult = $field->validate($environment, $userInput);

        $this->assertEquals($validationExpected, $validationResult);
    }

    /**
     * @expectedException CM_Exception_FormFieldValidation
     * @expectedExceptionMessage FormField Validation failed
     */
    public function testValidateInvalidToYoung() {
        $field = new SK_FormField_AgeRange();
        $environment = new CM_Frontend_Environment();
        $userInput = array('14', '20');
        $field->validate($environment, $userInput);
    }

    /**
     * @expectedException CM_Exception_FormFieldValidation
     * @expectedExceptionMessage FormField Validation failed
     */
    public function testValidateInvalidToOld() {
        $field = new SK_FormField_AgeRange();
        $environment = new CM_Frontend_Environment();
        $userInput = array('18', '120');
        $field->validate($environment, $userInput);
    }

    /**
     * @expectedException CM_Exception_FormFieldValidation
     * @expectedExceptionMessage FormField Validation failed
     */
    public function testValidateInvalidWrongParams() {
        $field = new SK_FormField_AgeRange();
        $environment = new CM_Frontend_Environment();
        $userInput = array(null, false);
        $field->validate($environment, $userInput);
    }
}
