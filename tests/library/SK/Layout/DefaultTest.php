<?php

class SK_Layout_DefaultTest extends SKTest_TestCase {

    public static function tearDownAfterClass() {
        CM_Db_Db::truncate('sk_affiliateProvider');
        parent::tearDownAfterClass();
    }

    public function testGuest() {
        $site = $this->getMockSite('SK_Site_Abstract');
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site));
        $page = $this->_createPage('SK_Page_About');
        $renderAdapter = new CM_RenderAdapter_Layout($render, $page);
        $html = new CM_Dom_NodeList($renderAdapter->fetch(), true);

        $this->assertHtmlExists($html, '#headerWrapper');
        $this->assertHtmlExists($html, '#navigation');
        $this->assertHtmlExists($html, '#middle');
        $this->assertHtmlExists($html, '#page');
        $this->assertFalse($html->has('#chat'));
    }

    public function testFreeuser() {
        $user = SKTest_TH::createUser();
        $site = $this->getMockSite('SK_Site_Abstract');
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site, $user));
        $page = $this->_createPage('SK_Page_About');
        $renderAdapter = new CM_RenderAdapter_Layout($render, $page);
        $html = new CM_Dom_NodeList($renderAdapter->fetch(), true);

        $this->assertHtmlExists($html, '#headerWrapper');
        $this->assertHtmlExists($html, '#navigation');
        $this->assertHtmlExists($html, '#middle');
        $this->assertHtmlExists($html, '#page');
        $this->assertHtmlExists($html, '#chat');
    }

    public function testCanonicalUrl() {
        $site = $this->getMockSite('SK_Site_Abstract', null, [
            'url' => 'http://www.my-fancy-site.dev',
        ]);
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site));
        $page = $this->_createPage('SK_Page_About');
        $renderAdapter = new CM_RenderAdapter_Layout($render, $page);
        $html = new CM_Dom_NodeList($renderAdapter->fetch(), true);

        $siteDefault = SK_Site_Abstract::factory();
        $canonicalUrl = $render->getUrlPage($page, $page->getParams()->getParamsEncoded(), $siteDefault);
        $this->assertContains('<link rel="canonical" href="' . $canonicalUrl . '">', $html->getHtml());
    }
}
