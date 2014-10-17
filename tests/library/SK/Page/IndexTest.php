<?php

class SK_Page_IndexTest extends SKTest_TestCase {

    public function testGuest() {
        $page = $this->_createPage('SK_Page_Index');
        $html = $this->_renderPage($page);

        $this->assertTrue($html->has('.SK_Component_SignIn'));
        $this->assertTrue($html->has('.SK_Component_SignUp'));
    }
}
