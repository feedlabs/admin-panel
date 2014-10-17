<?php

class SK_Component_EntityList_Profile_MatchesTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_EntityList_Profile_Matches();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_EntityList_Profile_Matches(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_EntityList_Profile_Matches'));
    }
}
