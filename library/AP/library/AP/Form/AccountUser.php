<?php

class AP_Form_AccountUser extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Email(['name' => 'email']));
        $this->registerField(new CM_FormField_Password(['name' => 'password']));

        $this->registerAction(new AP_FormAction_AccountUser_Save($this));
    }

    public function prepare(CM_Frontend_Environment $environment) {
        $this->getField('email')->setValue($environment->getViewer(true)->getEmail());
    }
}
