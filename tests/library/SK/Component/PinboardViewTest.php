<?php

class SK_Component_PinboardViewTest extends SKTest_TestCase {

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $user = SKTest_TH::createUser();
        $pinboard = SKTest_TH::createPinboard($user);

        $page = $this->_createPage('SK_Page_Pinboard', array('pinboard' => $pinboard));
        $html = $this->_renderPage($page, $viewer);

        $this->assertTrue($html->has('.SK_Component_EntityInteraction'));
        $this->assertNotContains('Edit', $html->find('button:eq(0)')->getAttribute('value'));
    }

    public function testOwner() {
        $user = SKTest_TH::createUser();
        $pinboard = SKTest_TH::createPinboard($user);

        $page = $this->_createPage('SK_Page_Pinboard', array('pinboard' => $pinboard));
        $html = $this->_renderPage($page, $user);

        $this->assertTrue($html->has('.SK_Component_EntityInteraction'));
        $this->assertContains('Edit', $html->find('button:eq(0)')->getAttribute('value'));
    }

    public function testPublic() {
        $viewer = $this->_createViewer();
        $user = SKTest_TH::createUser();
        $pinboard = SKTest_TH::createPinboard($user);

        $page = $this->_createPage('SK_Page_Pinboard', array('pinboard' => $pinboard));
        $html = $this->_renderPage($page, $user);

        $this->assertNotContains('.internals.asset.privacy.' . SK_ModelAsset_Entity_PrivacyAbstract::NONE . '.error.' .
            SK_Entity_Pinboard::getTypeStatic(), $html->getText());
        $this->assertNotContains('.internals.asset.privacy.' . SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY . '.error.' .
            SK_Entity_Pinboard::getTypeStatic(), $html->getText());
        $this->assertNotContains('.internals.asset.privacy.' . SK_ModelAsset_Entity_PrivacyAbstract::PERSONAL . '.error.' .
            SK_Entity_Pinboard::getTypeStatic(), $html->getText());
    }

    public function testFriend() {
        $viewer = $this->_createViewer();
        $user = SKTest_TH::createUser();
        $pinboard = SKTest_TH::createPinboard($user);

        $pinboard->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY);
        $page = $this->_createPage('SK_Page_Pinboard', array('pinboard' => $pinboard));
        $html = $this->_renderPage($page, $viewer);

        $this->assertContains('.internals.asset.privacy.' . SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY . '.error.' .
            SK_Entity_Pinboard::getTypeStatic(), $html->getText());

        $user->getFriends()->add($viewer);
        $html = $this->_renderPage($page, $viewer);
        $this->assertNotContains('.internals.asset.privacy.' . SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY . '.error.' .
            SK_Entity_Pinboard::getTypeStatic(), $html->getText());
    }

    public function testPersonal() {
        $viewer = $this->_createViewer();
        $user = SKTest_TH::createUser();
        $pinboard = SKTest_TH::createPinboard($user);

        $pinboard->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::PERSONAL);
        $page = $this->_createPage('SK_Page_Pinboard', array('pinboard' => $pinboard));
        $html = $this->_renderPage($page, $viewer);

        $this->assertContains('.internals.asset.privacy.' . SK_ModelAsset_Entity_PrivacyAbstract::PERSONAL . '.error.' .
            SK_Entity_Pinboard::getTypeStatic(), $html->getText());

        $user->getFriends()->add($viewer);
        $html = $this->_renderPage($page, $viewer);
        $this->assertContains('.internals.asset.privacy.' . SK_ModelAsset_Entity_PrivacyAbstract::PERSONAL . '.error.' .
            SK_Entity_Pinboard::getTypeStatic(), $html->getText());
    }
}
