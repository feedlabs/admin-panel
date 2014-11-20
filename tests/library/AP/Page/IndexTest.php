<?php

class AP_Page_IndexTest extends APTest_TestCase {

    public function testGuest() {
        $page = $this->_createPage('AP_Page_Index');
        $html = $this->_renderPage($page);

        $this->assertTrue($html->has('.AP_Component_SignIn'));
        $this->assertTrue($html->has('.AP_Component_SignUp'));
    }
}
