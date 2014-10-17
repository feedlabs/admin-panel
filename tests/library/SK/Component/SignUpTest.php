<?php

class SK_Component_SignUpTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_SignUp();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContainsAll(array('Username', 'Your Email', 'Password', 'I am', 'Looking For', 'City',
                'Birthday'), $page->find('form')->getText());
        $this->assertContains('you are 18 years or older', $page->find('.agreement')->getText());
        $this->assertContains('Sign Up', $page->find('.formAction')->getText());
    }
}
