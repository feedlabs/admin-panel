<?php

class SK_EntityQuery_VideoTest extends SKTest_TestCase {

    public function testConstruct() {
        $environment = new CM_Frontend_Environment();
        $entityQuery = new SK_EntityQuery_Video(new SK_Params());

        $this->assertEquals(3, $entityQuery->getEntityType());
        $this->assertEquals(array(
            'sort'     => 'created',
            'added'    => 'week',
            'category' => 'All',
            'term'     => '',
        ), $entityQuery->getUrlParams($environment));
    }
}
