<?php

class SK_EntityQuery_ProfileTest extends SKTest_TestCase {

    public function testConstruct() {
        $environment = new CM_Frontend_Environment();
        $entityQuery = new SK_EntityQuery_Profile(new SK_Params());

        $this->assertEquals(1, $entityQuery->getEntityType());
        $this->assertEquals(array(
            'sort'   => 'distance',
            'sex'    => 'all',
            'online' => 1,
            'photo'  => 0,
            'term'   => '',
        ), $entityQuery->getUrlParams($environment));
    }
}
