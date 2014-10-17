<?php

class SK_ModelAsset_Entity_ViewsTest extends SKTest_TestCase {

    public function testTrack() {
        $photo = SKTest_TH::createPhoto();
        $this->assertSame(0, $photo->getViews()->getViewCount());

        $photo->getViews()->track();
        $this->assertSame(1, $photo->getViews()->getViewCount());

        $photo->getViews()->track();
        $this->assertSame(2, $photo->getViews()->getViewCount());
    }

    public function testGetViewCount() {
        $photo = SKTest_TH::createPhoto();
        $this->assertSame(0, $photo->getViews()->getViewCount());
    }

    public function testEntityDelete() {
        $photo = SKTest_TH::createPhoto();

        $photo->getViews()->track();
        $this->assertSame(1, $photo->getViews()->getViewCount());

        $photo->delete();
        $this->assertNotRow(
            SK_ModelAsset_Entity_Views::getTableName(SK_Entity_Photo::getTypeStatic()),
            array('id' => $photo->getId())
        );
    }
}
