<?php

class AP_Form_Feed extends CM_Form_Abstract {

    protected function _initialize() {
        $this->registerField(new CM_FormField_Hidden(['name' => 'applicationId']));
        $this->registerField(new CM_FormField_Hidden(['name' => 'feedId']));
        $this->registerField(new CM_FormField_Text(['name' => 'name']));
        $this->registerField(new CM_FormField_Textarea(['name' => 'description']));

        $this->registerAction(new AP_FormAction_Feed_Create($this));
        $this->registerAction(new AP_FormAction_Feed_Save($this));
    }

    public function prepare(CM_Frontend_Environment $environment) {
        $params = $this->getParams();
        /** @var \Feedlabs\Feedify\Resource\Application $application */
        $application = $params->get('application');
        $this->getField('applicationId')->setValue($application->getId());

        if ($params->has('feed')) {
            /** @var \Feedlabs\Feedify\Resource\Feed $feed */
            $feed = $params->get('feed');
            $this->getField('feedId')->setValue($feed->getId());
            $this->getField('name')->setValue($feed->getName());
            $this->getField('description')->setValue($feed->getDescription());
        }
    }
}
