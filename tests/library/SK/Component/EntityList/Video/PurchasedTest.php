<?php

class SK_Component_EntityList_Video_PurchasedTest extends SKTest_TestCase {

    public function testRender() {
        $viewer = SKTest_TH::createUser();
        $cmp = new SK_Component_EntityList_Video_Purchased(null);
        $this->assertComponentAccessible($cmp, $viewer);

        $html = $this->_renderComponent($cmp, $viewer);
        $this->assertTrue($html->has('.SK_Component_EntityList_Video_Purchased'));
        $this->assertSame(0, $html->find('.SK_Component_EntityList_Video_Purchased [data-entity-id]')->count());

        $video = SKTest_TH::createVideo();
        $viewer->getVideoPurchasedList()->add($video);
        $html = $this->_renderComponent($cmp, $viewer);
        $this->assertSame(1, $html->find('.SK_Component_EntityList_Video_Purchased [data-entity-id]')->count());
        $this->assertTrue($html->has('.SK_Component_EntityList_Video_Purchased [data-entity-id="' . $video->getId() . '"]'));
    }
}
