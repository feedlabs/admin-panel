<?php

class AP_Form_ChangePassword extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Password(['name' => 'old_password']));
        $this->registerField(new CM_FormField_Password(['name' => 'new_password']));
        $this->registerField(new CM_FormField_Password(['name' => 'new_password_confirm']));

        $this->registerAction(new AP_FormAction_ChangePassword_Process($this));
    }
}
