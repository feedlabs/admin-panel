<?php

class SK_Component_EntityListTest extends SKTest_TestCase {

    public function testGuest() {
        $entityList[] = SKTest_TH::createPhoto();
        $entityList[] = SKTest_TH::createBlogpost();
        $entityList[] = SKTest_TH::createUser()->getProfile();
        $entityList[] = SKTest_TH::createVideo();
        $entityList[] = SKTest_TH::createPinboard();

        $behaviourOpen = $this->getMockBuilder('SK_Behaviour_Entity_Open')->setMethods(array('getHref'))->getMock();
        foreach ($entityList as $i => $entity) {
            $behaviourOpen->expects($this->at($i))->method('getHref')->with($entity)->will($this->returnValue('http://url-' . $i));
        }
        /** @var SK_Behaviour_Entity_Open $pagingSource */

        $site = $this->getMockSite('SK_Site_Abstract', 12345, null, array('getBehaviourEntityOpen'));
        $site->expects($this->any())->method('getBehaviourEntityOpen')->will($this->returnValue($behaviourOpen));
        /** @var CM_Site_Abstract $site */

        $pagingSource = new CM_PagingSource_Array($entityList);
        $paging = $this->getMockBuilder('CM_Paging_Abstract')->setConstructorArgs(array($pagingSource))->getMockForAbstractClass();
        /** @var CM_Paging_Abstract $paging */

        $cmp = new SK_Component_EntityList(array('list' => $paging));
        $page = $this->_renderComponent($cmp, null, $site);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.entityList'));
        $this->assertSame(5, $page->find('.entityList .entityListItem .entity .entity-link')->count());
        foreach ($entityList as $i => $entity) {
            $this->assertSame('http://url-' . $i, $page->find('.entityList .entityListItem:eq(' . $i . ') .entity .entity-link')->getAttribute('href'));
        }
    }
}
