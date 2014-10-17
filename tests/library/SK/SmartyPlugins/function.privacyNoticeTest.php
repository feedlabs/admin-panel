<?php

require_once CM_Util::getModulePath('SK') . 'library/SK/SmartyPlugins/function.privacyNotice.php';

class smarty_function_privacyNoticeTest extends CMTest_TestCase {

    /** @var Smarty_Internal_Template */
    private $_template;

    /** @var SK_Site_Abstract */
    private $_site;

    public function setUp() {
        $smarty = new Smarty();
        $this->_site = $this->getMockSite('SK_Site_Abstract');
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($this->_site));
        $this->_template = $smarty->createTemplate('string:');
        $this->_template->assignGlobal('render', $render);
    }

    public function testPublic() {
        $user = SKTest_TH::createUser(null, null, $this->_site);
        $photo = SKTest_TH::createPhoto($user);
        $video = SKTest_TH::createVideo($user);
        $blog = SKTest_TH::createBlogpost($user);
        $privacy = SK_ModelAsset_Entity_PrivacyAbstract::NONE;

        $this->_assertSame(".internals.asset.privacy." . $privacy . ".error." . $photo->getType(), array('entity' => $photo));
        $this->_assertSame(".internals.asset.privacy." . $privacy . ".error." . $video->getType(), array('entity' => $video));
        $this->_assertSame(".internals.asset.privacy." . $privacy . ".error." . $blog->getType(), array('entity' => $blog));
    }

    public function testFriends() {
        $user = SKTest_TH::createUser(null, null, $this->_site);
        $photo = SKTest_TH::createPhoto($user);
        $video = SKTest_TH::createVideo($user);
        $blog = SKTest_TH::createBlogpost($user);
        $privacy = SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY;

        $photo->getPrivacy()->set($privacy);
        $video->getPrivacy()->set($privacy);
        $blog->getPrivacy()->set($privacy);

        $this->_assertSame(".internals.asset.privacy." . $privacy . ".error." . $photo->getType(), array('entity' => $photo));
        $this->_assertSame(".internals.asset.privacy." . $privacy . ".error." . $video->getType(), array('entity' => $video));
        $this->_assertSame(".internals.asset.privacy." . $privacy . ".error." . $blog->getType(), array('entity' => $blog));
    }

    public function testPersonal() {
        $user = SKTest_TH::createUser(null, null, $this->_site);
        $photo = SKTest_TH::createPhoto($user);
        $video = SKTest_TH::createVideo($user);
        $blog = SKTest_TH::createBlogpost($user);
        $privacy = SK_ModelAsset_Entity_PrivacyAbstract::PERSONAL;

        $photo->getPrivacy()->set($privacy);
        $video->getPrivacy()->set($privacy);
        $blog->getPrivacy()->set($privacy);

        $this->_assertSame(".internals.asset.privacy." . $privacy . ".error." . $photo->getType(), array('entity' => $photo));
        $this->_assertSame(".internals.asset.privacy." . $privacy . ".error." . $video->getType(), array('entity' => $video));
        $this->_assertSame(".internals.asset.privacy." . $privacy . ".error." . $blog->getType(), array('entity' => $blog));
    }

    public function testSkipHref() {
        $language = SKTest_TH::createLanguage();
        $smarty = new Smarty();
        $site = $this->getMockSite('SK_Site_Abstract');
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site, null, $language));
        $template = $smarty->createTemplate('string:');
        $template->assignGlobal('render', $render);

        $user = SKTest_TH::createUser(null, null, $this->_site);
        $photo = SKTest_TH::createPhoto($user);
        $privacy = SK_ModelAsset_Entity_PrivacyAbstract::NONE;

        $language->setTranslation(
            ".internals.asset.privacy." . $privacy . ".error." . $photo->getType(), 'Private {$username} entity', array('username'));

        $this->_assertContains('href', array('entity' => $photo, 'skipHref' => false), $template);
        $this->_assertNotContains('href', array('entity' => $photo, 'skipHref' => true), $template);
    }

    /**
     * @param string $expected
     * @param array  $params
     */
    private function _assertSame($expected, array $params) {
        $this->assertSame($expected, smarty_function_privacyNotice($params, $this->_template));
    }

    /**
     * @param string                            $expected
     * @param array                             $params
     * @param Smarty_Internal_TemplateBase|null $template
     */
    private function _assertContains($expected, array $params, $template = null) {
        if (!$template) {
            $template = $this->_template;
        }
        $this->assertContains($expected, smarty_function_privacyNotice($params, $template));
    }

    /**
     * @param string                            $expected
     * @param array                             $params
     * @param Smarty_Internal_TemplateBase|null $template
     */
    private function _assertNotContains($expected, array $params, $template = null) {
        if (!$template) {
            $template = $this->_template;
        }
        $this->assertNotContains($expected, smarty_function_privacyNotice($params, $template));
    }
}
