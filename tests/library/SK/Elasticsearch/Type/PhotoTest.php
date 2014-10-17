<?php

class SK_Elasticsearch_Type_PhotoTest extends SKTest_TestCase {

    /** @var SK_Elasticsearch_Type_Photo */
    protected $_type;

    /** @var CM_Elasticsearch_Index_Cli */
    protected $_searchIndexCli;

    public function setUp() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;

        $this->_type = new SK_Elasticsearch_Type_Photo();
        $this->_searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $this->_searchIndexCli->create($this->_type->getIndex()->getName());
    }

    public function tearDown() {
        $this->_type->getIndex()->delete();
        SKTest_TH::clearEnv();
    }

    public function testBlocked() {
        $user = SKTest_TH::createUser();
        SKTest_TH::createPhoto($user);
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Photo();
        $searchQuery->filterNonblocked();

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $paging->getCount());

        $user->setBlocked();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(0, $paging->getCount());
    }

    public function testEntertainer() {
        $user = SKTest_TH::createUser();
        SKTest_TH::createPhoto($user);
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Photo();
        $searchQuery->filterRoleNot(SK_Role::ENTERTAINER);

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $paging->getCount());

        $user->getRoles()->add(SK_Role::ENTERTAINER);
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(0, $paging->getCount());
    }

    public function testTags() {
        $photo1 = SKTest_TH::createPhoto();
        $photo1->getTags()->set(array('foo', 'bar'));
        $photo2 = SKTest_TH::createPhoto();
        $photo2->getTags()->set(array('foo'));
        $photo3 = SKTest_TH::createPhoto();
        $photo3->getTags()->set(array('Me ga'));
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Photo();
        $searchQuery->filterTags(array('foo', 'bar'));
        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(2, $pagingSource->getCount());
        $this->assertContainsAll(array($photo1->getId(), $photo2->getId()), $pagingSource->getItems());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Photo();
        $searchQuery->filterTags('bar###');
        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($photo1->getId()), $pagingSource->getItems());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Photo();
        $searchQuery->filterTags('me Ga');
        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($photo3->getId()), $pagingSource->getItems());
    }

    public function testSetBirthdate() {
        $user1 = SKTest_TH::createUser();
        $user1->setBirthdate(new \DateTime('-20 years'));
        $photo1 = SKTest_TH::createPhoto($user1);

        $user2 = SKTest_TH::createUser();
        $user2->setBirthdate(new \DateTime('-23 years'));
        $photo2 = SKTest_TH::createPhoto($user2);

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Photo();
        $searchQuery->filterAgeRange(18, 25);

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(2, $pagingSource->getCount());
        $this->assertContainsAll(array($photo1->getId(), $photo2->getId()), $pagingSource->getItems());

        $user1->setBirthdate(new \DateTime('-30 years'));

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($photo2->getId()), $pagingSource->getItems());
    }
}
