<?php

class AP_Form_Feed extends CM_Form_Abstract {

    protected function _initialize() {
        $applicationList = array(
            '' => '- Select -',
            '8457vb738578v' => 'Hallo123',
            'skjdfhksdahfd' => 'sadhkfkjh kjadfsjh ',
        );

        // todo: get all applications from API

        $this->registerField(new CM_FormField_Set_Select(['name' => 'application', 'values' => $applicationList, 'labelsInValues' => true]));
        $this->registerField(new CM_FormField_Text(['name' => 'name']));

        // todo: add description

        $this->registerAction(new AP_FormAction_Feed_Create($this));
    }
}
