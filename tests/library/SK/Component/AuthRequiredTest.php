<?php

class SK_Component_AuthRequiredTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_AuthRequired();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContainsAll(array('SK_Component_SignIn', 'SK_Component_SignUp'), $page->getHtml());
    }
}
