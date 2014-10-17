<?php

class SK_Component_ResetPasswordTest extends SKTest_TestCase {

    public function testGuest() {
        // Valid code
        $user = SKTest_TH::createUser();
        $params = array('code' => SK_App_ResetPassword::generateCode($user), 'user' => $user);
        $cmp = new SK_Component_ResetPassword($params);
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContainsAll(array('New Password', 'Reset Password'), $page->find('form')->getText());

        // Invalid code
        $params = array('code' => SK_App_ResetPassword::generateCode($user) . 'asdf', 'user' => $user);
        $page = $this->_renderComponent(new SK_Component_ResetPassword($params));

        $this->assertComponentAccessible($cmp);
        $this->assertContains('Sorry your code is incorrect or has expired.', $page->find('.box-body')->getText());
    }
}
