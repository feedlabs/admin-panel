<?php

class AP_Form_Application extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Text(['name' => 'name']));
        $this->registerField(new CM_FormField_Textarea(['name' => 'description']));

        $this->registerAction(new AP_FormAction_Application_Create($this));
    }
}
