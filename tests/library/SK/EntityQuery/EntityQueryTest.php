<?php

class SK_EntityQuery_EntityQueryTest extends SKTest_TestCase {

    public function testConstruct() {
        $environment = new CM_Frontend_Environment();
        $entityQuery = new SK_EntityQuery_EntityQuery(new SK_Params());

        $this->assertNull($entityQuery->getEntityType());
        $this->assertEquals(array('sort' => 'created', 'term' => ''), $entityQuery->getUrlParams($environment));
    }

    public function testConstructWithParams() {
        $environment = new CM_Frontend_Environment();
        $entityQuery = new SK_EntityQuery_EntityQuery(new SK_Params(array('sort' => 'rating')));

        $this->assertEquals(array('sort' => 'rating', 'term' => null), $entityQuery->getUrlParams($environment));
    }

    /**
     * @expectedException CM_Exception_InvalidParam
     * @expectedExceptionMessage Invalid value `foo` for param `sort`
     */
    public function testConstructWithWrongParams() {
        new SK_EntityQuery_EntityQuery(new SK_Params(array('sort' => 'foo')), new CM_Frontend_Environment());
    }

    public function testToArrayFromArray() {
        $entityQuery = new SK_EntityQuery_EntityQuery(new SK_Params(array('sort' => 'rating')));

        $entityQueryConverted = SK_EntityQuery_EntityQuery::fromArray($entityQuery->toArray());
        $this->assertInstanceOf('SK_EntityQuery_EntityQuery', $entityQueryConverted);
        $urlParams = $entityQueryConverted->getUrlParams(new CM_Frontend_Environment(), true);
        $this->assertSame(array('sort' => 'rating', 'term' => null), $urlParams);
    }
}
