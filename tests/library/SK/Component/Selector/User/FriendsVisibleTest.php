<?php

class SK_Component_Selector_User_FriendsVisibleTest extends SKTest_TestCase {

    public function testGuest() {
        $this->assertComponentNotAccessible(new SK_Component_Selector_User_FriendsVisible());
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $friend = SKTest_TH::createUser();
        $viewer->getFriends()->add($friend);
        $friend->setOnline(true);
        $cmp = new SK_Component_Selector_User_FriendsVisible(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains($friend->getUsername(), $page->getText());
    }

    public function testFreeuserEmpty() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_Selector_User_FriendsVisible(null);

        $this->assertComponentNotRenderable($cmp, $viewer, 'CM_Exception_Invalid');
    }
}
