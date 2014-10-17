<?php

class SK_Component_FeedTest extends SKTest_TestCase {

    public function testGuest() {
        $user = SKTest_TH::createUser();

        // Without activity
        $cmp = new SK_Component_Feed(array('viewClassName' => 'SK_Component_FeedList_Popular', 'user' => $user));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_FeedList_Popular'));
        $this->assertTrue($page->has('.showMore'));
        $this->assertTrue($page->has('.showNextPage'));
    }
}
