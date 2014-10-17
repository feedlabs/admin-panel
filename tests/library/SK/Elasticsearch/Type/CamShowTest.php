<?php

class SK_Elasticsearch_Type_CamShowTest extends SKTest_TestCase {

    /** @var SK_Elasticsearch_Type_CamShow */
    protected $_type;

    /** @var CM_Elasticsearch_Index_Cli */
    protected $_searchIndexCli;

    public function setUp() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;

        $this->_type = new SK_Elasticsearch_Type_CamShow();
        $this->_searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $this->_searchIndexCli->create($this->_type->getIndex()->getName());
    }

    public function tearDown() {
        $this->_type->getIndex()->delete();
        SKTest_TH::clearEnv();
    }

    public function testPaging() {
        $searchQuery = new SK_Elasticsearch_Query_Entity_CamShow();
        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(0, $paging->getCount());

        SKTest_TH::createCamShow();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $paging->getCount());
    }

    public function testSetBirthdate() {
        $user1 = SKTest_TH::createUser();
        $user1->setBirthdate(new \DateTime('-20 years'));
        $camshow1 = SKTest_TH::createCamShow($user1);

        $user2 = SKTest_TH::createUser();
        $user2->setBirthdate(new \DateTime('-23 years'));
        $camshow2 = SKTest_TH::createCamShow($user2);

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_CamShow();
        $searchQuery->filterAgeRange(18, 25);

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(2, $pagingSource->getCount());
        $this->assertContainsAll(array($camshow1->getId(), $camshow2->getId()), $pagingSource->getItems());

        $user1->setBirthdate(new \DateTime('-30 years'));

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($camshow2->getId()), $pagingSource->getItems());
    }
}
