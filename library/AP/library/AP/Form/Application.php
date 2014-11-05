<?php

class AP_Form_Application extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Hidden(['name' => 'applicationId']));
        $this->registerField(new CM_FormField_Text(['name' => 'name']));
        $this->registerField(new CM_FormField_Textarea(['name' => 'description']));

        $this->registerAction(new AP_FormAction_Application_Create($this));
        $this->registerAction(new AP_FormAction_Application_Save($this));
    }

    public function prepare(CM_Frontend_Environment $environment) {
        $params = $this->getParams();
        if ($params->has('application')) {
            /** @var \Feedlabs\Feedify\Resource\Application $application */
            $application = $params->get('application');
            $this->getField('applicationId')->setValue($application->getId());
            $this->getField('name')->setValue($application->getName());
            $this->getField('description')->setValue($application->getDescription());
        }
    }
}
