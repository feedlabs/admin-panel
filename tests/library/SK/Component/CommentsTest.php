<?php

class SK_Component_CommentsTest extends SKTest_TestCase {

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $video = SKTest_TH::createVideo();
        $user = SKTest_TH::createUser();
        SK_Entity_Comment::create($user, $video, 'Blabla');
        $cmp = new SK_Component_Comments(array('entity' => $video));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Blabla', $page->find('.content')->getText());
        $this->assertContains($user->getUsername(), $page->find('.username')->getText());
        $this->assertTrue($page->has('.comment'));

        //deleted user
        $user->delete();
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertNotContains('Blabla', $page->find('.content')->getText());
        $this->assertNotContains('Deleted Member', $page->find('.username')->getText());
        $this->assertFalse($page->has('.comment'));
    }
}
