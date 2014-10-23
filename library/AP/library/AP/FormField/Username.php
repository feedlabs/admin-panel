<?php

class AP_FormField_Username extends CM_FormField_Text {

    protected function _initialize() {
        $this->_options['allowViewer'] = $this->_params->getBoolean('allowViewer', false);
        $this->_params->set('lengthMax', 32);
        parent::_initialize();
    }

    public function validate(CM_Frontend_Environment $environment, $userInput) {
        $userInput = parent::validate($environment, $userInput);

        if (!AP_Model_User::usernameIsValid($userInput)) {
            throw new CM_Exception_FormFieldValidation('Username may only contain alphanumerical characters.');
        }

        /** @var AP_Model_User $viewer */
        $viewer = $environment->getViewer();
        $equalsViewer = $viewer && $this->_options['allowViewer'] && $userInput == $viewer->getUsername();
        if (!$equalsViewer && AP_Model_User::findUsername($userInput)) {
            throw new CM_Exception_FormFieldValidation('Username already taken');
        }

        $badwordList = new CM_Paging_ContentList_Badwords();
        if ($badword = $badwordList->getMatch($userInput)) {
            throw new CM_Exception_FormFieldValidation('Sorry, but we don\'t accept user names containing "{$badword}"', array('badword' => $badword));
        }

        return $userInput;
    }
}
