<?php

class SK_EntityQuery_PinboardTest extends SKTest_TestCase {

    public function testConstruct() {
        $environment = new CM_Frontend_Environment();
        $entityQuery = new SK_EntityQuery_Pinboard(new SK_Params());

        $this->assertEquals(140, $entityQuery->getEntityType());
        $this->assertEquals(array('sort' => 'created', 'added' => 'week', 'term' => ''), $entityQuery->getUrlParams($environment));
    }
}
