<?php

class AP_FormAction_SignUp_Create extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('username', 'email', 'password', 'sex', 'match_sex', 'location', 'birthdate');
    }

    protected function _checkData(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        if (AP_Model_User::findEmail($params->getString('email'))) {
            $response->addError($response->getRender()->getTranslation('This email address is already used.'), 'email');
        }
        if ($response->getRequest()->getIpBlocked()) {
            $response->addError($response->getRender()->getTranslation('Your IP address is blocked.'));
        }
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $data = $params->getParamsDecoded();
        $ip = $response->getRequest()->getIp();
        if ($ip) {
            $data['join_ip'] = $ip;
        }
        $data['site'] = $response->getSite();
        $data['language'] = $response->getRequest()->getLanguage();

        $user = AP_Model_User::createStatic($data);

        $response->getRequest()->getSession()->setUser($user);
        $response->getRequest()->getSession()->set('firstvisit', true);

        $response->reloadPage();
    }
}
