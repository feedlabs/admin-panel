<?php

class SK_Component_SignUpFormTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_SignUpForm();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContainsAll(array('Username', 'Your Email', 'Password', 'I am', 'Looking For', 'City', 'Birthday'), $page->getText('form'));
        $this->assertContains('Sign Up', $page->getText('.formAction'));
    }
}
