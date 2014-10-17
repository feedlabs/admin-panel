<?php

class SK_Layout_IndexTest extends SKTest_TestCase {

    public static function tearDownAfterClass() {
        CM_Db_Db::truncate('sk_affiliateProvider');
        parent::tearDownAfterClass();
    }

    public function testGuest() {
        $render = new CM_Frontend_Render();
        $page = new SK_Page_Index();
        $renderAdapter = new CM_RenderAdapter_Layout($render, $page);
        $html = new CM_Dom_NodeList($renderAdapter->fetch(), true);

        $this->assertHtmlExists($html, '#header');
        $this->assertHtmlExists($html, '#page');
        $this->assertFalse($html->has('#chat'));
    }

    public function testFreeuser() {
        $viewer = SKTest_TH::createUser();
        $page = new SK_Page_Index();

        $render = new CM_Frontend_Render(new CM_Frontend_Environment(null, $viewer));
        $renderAdapter = new CM_RenderAdapter_Layout($render, $page);
        $html = new CM_Dom_NodeList($renderAdapter->fetch(), true);

        $this->assertHtmlExists($html, '#header');
        $this->assertHtmlExists($html, '#page');
        $this->assertFalse($html->has('#chat'));
    }
}
