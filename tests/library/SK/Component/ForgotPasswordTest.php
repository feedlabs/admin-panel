<?php

class SK_Component_ForgotPasswordTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_ForgotPassword();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('Email', $page->find('label')->getText());
    }
}
