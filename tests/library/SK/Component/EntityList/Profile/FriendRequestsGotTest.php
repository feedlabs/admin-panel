<?php

class SK_Component_EntityList_Profile_FriendRequestsGotTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_EntityList_Profile_FriendRequestsGot();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        SKTest_TH::createUser()->getFriendRequestsSent()->add($viewer);
        $cmp = new SK_Component_EntityList_Profile_FriendRequestsGot(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.entity'));
        $this->assertTrue($page->has('.action'));
    }
}
