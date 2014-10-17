<?php

class SK_Component_EntityList_EntityQueryTest extends SKTest_TestCase {

    public function testGuest() {
        $entityQuery = new SK_EntityQuery_EntityQuery(new CM_Params(), new CM_Frontend_Environment());
        $cmp = new SK_Component_EntityList_EntityQuery(array('entityQuery' => $entityQuery));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_EntityList_EntityQuery'));
    }

    public function testReload() {
        $entityQuery = new SK_EntityQuery_EntityQuery(new CM_Params(), new CM_Frontend_Environment());
        $component = new SK_Component_EntityList_EntityQuery(array('entityQuery' => $entityQuery));
        $scopeView = new CM_Frontend_ViewResponse($component);
        $request = $this->createRequestAjax($component, 'reloadComponent', null, $scopeView, $scopeView);
        $response = $this->processRequest($request);
        $this->assertViewResponseSuccess($response);
    }
}
