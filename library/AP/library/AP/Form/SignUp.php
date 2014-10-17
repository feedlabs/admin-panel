<?php

class AP_Form_SignUp extends CM_Form_Abstract {

    protected function _initialize() {
        $site = $this->getParams()->getSite('site');
        $this->registerField(new AP_FormField_Username(['name' => 'username']));
        $this->registerField(new CM_FormField_Email(['name' => 'email']));
        $this->registerField(new CM_FormField_Password(['name' => 'password']));
        $this->registerField(new AP_FormField_Sex(['name' => 'sex', 'site' => $site]));
        $this->registerField(new AP_FormField_SexSet(['name' => 'match_sex', 'site' => $site]));
        $this->registerField(new CM_FormField_Location(['name' => 'location', 'levelMin' => CM_Model_Location::LEVEL_CITY]));
        $this->registerField(new AP_FormField_Age(['name' => 'birthdate']));

        $this->registerAction(new AP_FormAction_SignUp_Create($this));
    }

    public function prepare(CM_Frontend_Environment $environment) {
        if ($this->getParams()->has('values')) {
            $userInputList = $this->getParams()->getArray('values');
            $validValues = $this->_validateValues($userInputList, $environment);
            $this->_setValues($validValues);
        }

        /** @var CM_FormField_Location $locationField */
        $locationField = $this->getField('location');
        $locationField->setValueByEnvironment($environment);
    }
}
