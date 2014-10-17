<?php

class SK_Component_SignInTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_SignIn();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContainsAll(array('Forgot Password', 'Login', 'Remember Me'), $page->find('form.sign_in')->getText());
        $this->assertSame('Username/Email', $page->find('input[name="login"]')->getAttribute('placeholder'));
        $this->assertSame('Password', $page->find('input[name="password"]')->getAttribute('placeholder'));
    }
}
