<?php

class SK_EntityQuery_PhotoTest extends SKTest_TestCase {

    public function testConstruct() {
        $environment = new CM_Frontend_Environment();
        $entityQuery = new SK_EntityQuery_Photo(new SK_Params());

        $this->assertEquals(2, $entityQuery->getEntityType());
        $this->assertEquals(array('sort' => 'created', 'added' => 'week', 'sex' => 'all', 'term' => ''), $entityQuery->getUrlParams($environment));
    }

    public function testToArrayFromArray() {
        $entityQuery = new SK_EntityQuery_Photo(new SK_Params(array('sort' => 'rating')));

        $entityQueryConverted = SK_EntityQuery_Photo::fromArray($entityQuery->toArray());
        $this->assertInstanceOf('SK_EntityQuery_Photo', $entityQueryConverted);
        $urlParams = $entityQueryConverted->getUrlParams(new CM_Frontend_Environment(), true);
        $this->assertSame(array('sort' => 'rating', 'term' => null), $urlParams);
    }
}
