<?php

class SK_Form_SignInTest extends SKTest_TestCase {

    public function testProcess() {
        $user = SKTest_TH::createUser();
        $user->setPassword('awYeah!3');

        $form = new SK_Form_SignIn();
        $formAction = new SK_FormAction_SignIn_Process($form);
        $data = [
            'login'    => $user->getUsername(),
            'password' => 'awYeah!3'
        ];
        $request = $this->createRequestFormAction($formAction, $data);

        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseSuccess($response);
        $responseContent = json_decode($response->getContent(), true);
        $this->assertTrue($user->equals($response->getRequest()->getSession()->getUser()));
        $this->assertContains('location.reload', $responseContent['success']['exec']);
    }

    public function testWrongPassword() {
        $user = SKTest_TH::createUser();
        $user->setPassword('asdf');

        $form = new SK_Form_SignIn();
        $formAction = new SK_FormAction_SignIn_Process($form);
        $data = [
            'login'    => $user->getUsername(),
            'password' => 'fdsa'
        ];
        $request = $this->createRequestFormAction($formAction, $data);

        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseError($response, 'Password and Username do not match', 'password');
    }

    public function testWrongUsername() {
        $user = SKTest_TH::createUser();
        $user->setPassword('asdf');

        $form = new SK_Form_SignIn();
        $formAction = new SK_FormAction_SignIn_Process($form);
        $data = [
            'login'    => 'asdfasdf',
            'password' => 'asdf'
        ];
        $request = $this->createRequestFormAction($formAction, $data);

        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseError($response, 'Password and Username do not match', 'password');
    }

    public function testUsernameCaseSwitch() {
        $user = SKTest_TH::createUser();
        $user->setPassword('asdf');
        $user->setUsername('AnoNymous');

        $form = new SK_Form_SignIn();
        $formAction = new SK_FormAction_SignIn_Process($form);
        $data = [
            'login'    => 'aNOnYMOUS',
            'password' => 'asdf'
        ];
        $request = $this->createRequestFormAction($formAction, $data);

        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseSuccess($response);
    }
}
