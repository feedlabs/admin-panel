<?php

class AP_Form_SignUp extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new AP_FormField_Username(['name' => 'username']));
        $this->registerField(new CM_FormField_Email(['name' => 'email']));
        $this->registerField(new CM_FormField_Password(['name' => 'password']));

        $this->registerAction(new AP_FormAction_SignUp_Create($this));
    }
}
