<?php

class AP_Form_UserPermissions extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Boolean(['name' => 'application_view']));
        $this->registerField(new CM_FormField_Boolean(['name' => 'application_edit']));
        $this->registerField(new CM_FormField_Boolean(['name' => 'application_delete']));
        $this->registerField(new CM_FormField_Boolean(['name' => 'feed_view']));
        $this->registerField(new CM_FormField_Boolean(['name' => 'feed_edit']));
        $this->registerField(new CM_FormField_Boolean(['name' => 'feed_delete']));
        $this->registerField(new CM_FormField_Boolean(['name' => 'user_view']));
        $this->registerField(new CM_FormField_Boolean(['name' => 'user_edit']));
        $this->registerField(new CM_FormField_Boolean(['name' => 'user_delete']));
    }

    public function prepare(CM_Frontend_Environment $environment) {
        $user = $this->getParams()->getString('user');
        // todo: get user permissions over API and set to formFields
    }
}
