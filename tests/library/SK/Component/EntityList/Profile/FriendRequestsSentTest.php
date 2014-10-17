<?php

class SK_Component_EntityList_Profile_FriendRequestsSentTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_EntityList_Profile_FriendRequestsSent();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $viewer->getFriendRequestsSent()->add(SKTest_TH::createUser());
        $cmp = new SK_Component_EntityList_Profile_FriendRequestsSent(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.entity'));
        $this->assertTrue($page->has('.action'));
    }
}
