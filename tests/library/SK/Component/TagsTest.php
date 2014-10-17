<?php

class SK_Component_TagsTest extends SKTest_TestCase {

    public function testGuest() {
        // No Tags
        $video = SKTest_TH::createVideo();
        $cmp = new SK_Component_Tags(array('entity' => $video));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertEquals(null, $page->find('.tag')->getText());

        // With Tags
        $video = SKTest_TH::createVideo();
        $video->getTags()->set(array('foo', 'bar'));
        $cmp = new SK_Component_Tags(array('entity' => $video));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('/search?term=foo&amp;type=video"', $page->getHtml());
        $this->assertContains('foo', $page->find('.box-body')->getText());
        $this->assertContains('bar', $page->find('.box-body')->getText());
    }
}
