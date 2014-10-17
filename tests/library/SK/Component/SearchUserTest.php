<?php

class SK_Component_SearchUserTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_SearchUser(array('query' => array()));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('Search For', $page->getText());
    }
}
