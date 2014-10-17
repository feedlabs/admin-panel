<?php

class SK_Form_ChangePasswordTest extends SKTest_TestCase {

    public function testProcessAction() {
        $form = new SK_Form_ChangePassword();
        $formAction = new SK_FormAction_ChangePassword_Process($form);
        $data = ["old_password"         => "blabla",
                 "new_password"         => "blabla1",
                 "new_password_confirm" => "blabla1"
        ];
        $user = SKTest_TH::createUser();
        $user->setPassword('blabla');

        $request = $this->createRequestFormAction($formAction, $data);
        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseSuccess($response, 'Password changed.');
        $this->assertTrue((bool) SK_App_Auth::checkLogin($user->getUsername(), 'blabla1'));
    }

    public function testWrongUser() {
        $form = new SK_Form_ChangePassword();
        $formAction = new SK_FormAction_ChangePassword_Process($form);
        $data = ["old_password"         => "blabla",
                 "new_password"         => "blabla1",
                 "new_password_confirm" => "blabla1"
        ];
        $user = SKTest_TH::createUser();
        $user->setPassword('blabla');
        $wrongUser = SKTest_TH::createUser();

        $request = $this->createRequestFormAction($formAction, $data);
        $response = $this->processRequestWithViewer($request, $wrongUser);
        $this->assertFormResponseError($response, 'Incorrect old password.', 'old_password');
        $this->assertFalse((bool) SK_App_Auth::checkLogin($user->getUsername(), 'blabla1'));
    }
}
