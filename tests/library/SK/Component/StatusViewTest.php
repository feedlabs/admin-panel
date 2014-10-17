<?php

class SK_Component_StatusViewTest extends SKTest_TestCase {

    public function testGuest() {
        $user = SKTest_TH::createUser();
        $status = SK_Entity_Status::create($user, 'foo bar');
        $cmp = new SK_Component_StatusView(array('status' => $status));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('foo bar', $page->find('.status')->getText());
        $this->assertTrue($page->has('.SK_Component_Comments'));
    }
}
