<?php

class SK_Layout_PlainTest extends SKTest_TestCase {

    public function testGuest() {
        $viewer = CM_Model_User::createStatic();
        $site = $this->getMockSite('SK_Site_Abstract');
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site, $viewer));
        $page = new SK_Page_Payment_Denial();
        $renderAdapter = new CM_RenderAdapter_Layout($render, $page);
        $html = new CM_Dom_NodeList($renderAdapter->fetch(), true);

        $this->assertFalse($html->has('#headerWrapper'));
        $this->assertFalse($html->has('#navigation'));
        $this->assertFalse($html->has('#middle'));
        $this->assertFalse($html->has('#page'));
        $this->assertFalse($html->has('#chat'));
    }

    public function testFreeuser() {
        $site = $this->getMockSite('SK_Site_Abstract');
        $viewer = CM_Model_User::createStatic();
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site, $viewer));
        $page = new SK_Page_Payment_Denial();
        $renderAdapter = new CM_RenderAdapter_Layout($render, $page);
        $html = new CM_Dom_NodeList($renderAdapter->fetch(), true);

        $this->assertFalse($html->has('#headerWrapper'));
        $this->assertFalse($html->has('#navigation'));
        $this->assertFalse($html->has('#middle'));
        $this->assertFalse($html->has('#page'));
        $this->assertFalse($html->has('#chat'));
    }
}
