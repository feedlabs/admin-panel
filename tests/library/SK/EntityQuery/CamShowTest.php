<?php

class SK_EntityQuery_CamShowTest extends SKTest_TestCase {

    public function testConstruct() {
        $environment = new CM_Frontend_Environment();
        $entityQuery = new SK_EntityQuery_CamShow(new SK_Params());

        $this->assertEquals(167, $entityQuery->getEntityType());
        $this->assertEquals(array('sex' => 'all', 'sort' => 'lastOnline', 'photo' => 1, 'term' => null), $entityQuery->getUrlParams($environment));
    }
}
