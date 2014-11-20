<?php

class AP_Form_SignIn extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Text(['name' => 'login']));
        $this->registerField(new CM_FormField_Password(['name' => 'password']));
        $this->registerField(new CM_FormField_Boolean(['name' => 'remember_me']));

        $this->registerAction(new AP_FormAction_SignIn_Process($this));
    }
}
