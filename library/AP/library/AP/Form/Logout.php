<?php

class AP_Form_Logout extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Hidden(['name' => 'redirectPage']));
        $this->registerAction(new AP_FormAction_Logout_Process($this));
    }

    public function prepare(CM_Frontend_Environment $environment) {
        $redirectPage = $this->getParams()->getString('redirectPage', 'AP_Page_Index');
        $this->getField('redirectPage')->setValue($redirectPage);
    }
}
