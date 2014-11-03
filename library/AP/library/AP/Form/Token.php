<?php

class AP_Form_Token extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Text(['name' => 'name']));

        $this->registerAction(new AP_FormAction_Token_Create($this));
    }
}
