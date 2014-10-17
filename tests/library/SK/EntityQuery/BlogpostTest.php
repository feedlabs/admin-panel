<?php

class SK_EntityQuery_BlogpostTest extends SKTest_TestCase {

    public function testConstruct() {
        $environment = new CM_Frontend_Environment();
        $entityQuery = new SK_EntityQuery_Blogpost(new SK_Params());

        $this->assertEquals(4, $entityQuery->getEntityType());
        $this->assertEquals(array('sort' => 'created', 'added' => 'week', 'term' => ''), $entityQuery->getUrlParams($environment));
    }
}
