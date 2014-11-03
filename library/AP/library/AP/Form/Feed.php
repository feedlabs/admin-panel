<?php

class AP_Form_Feed extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Hidden(['name' => 'application']));
        $this->registerField(new CM_FormField_Text(['name' => 'name']));

        // todo: add description

        $this->registerAction(new AP_FormAction_Feed_Create($this));
    }

    public function prepare(CM_Frontend_Environment $environment) {
        $this->getField('application')->setValue($this->getParams()->get('application'));
    }
}
