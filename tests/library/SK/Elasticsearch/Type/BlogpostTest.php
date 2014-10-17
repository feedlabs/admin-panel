<?php

class SK_Elasticsearch_Type_BlogpostTest extends SKTest_TestCase {

    /** @var SK_Elasticsearch_Type_Blogpost */
    protected $_type;

    /** @var CM_Elasticsearch_Index_Cli */
    protected $_searchIndexCli;

    public function setUp() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;

        $this->_type = new SK_Elasticsearch_Type_Blogpost();
        $this->_searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $this->_searchIndexCli->create($this->_type->getIndex()->getName());
    }

    public function tearDown() {
        $this->_type->getIndex()->delete();
        SKTest_TH::clearEnv();
    }

    public function testEntertainer() {
        $user = SKTest_TH::createUser();
        SKTest_TH::createBlogpost($user);
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Blogpost();
        $searchQuery->filterRoleNot(SK_Role::ENTERTAINER);

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $paging->getCount());

        $user->getRoles()->add(SK_Role::ENTERTAINER);
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(0, $paging->getCount());
    }

    public function testTags() {
        $blogpost1 = SKTest_TH::createBlogpost();
        $blogpost1->getTags()->set(array('foo', 'bar'));
        $blogpost2 = SKTest_TH::createBlogpost();
        $blogpost2->getTags()->set(array('foo'));
        $blogpost3 = SKTest_TH::createBlogpost();
        $blogpost3->getTags()->set(array('Me ga'));
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Blogpost();
        $searchQuery->filterTags(array('foo', 'bar'));
        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(2, $pagingSource->getCount());
        $this->assertContainsAll(array($blogpost1->getId(), $blogpost2->getId()), $pagingSource->getItems());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Blogpost();
        $searchQuery->filterTags('bar###');
        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($blogpost1->getId()), $pagingSource->getItems());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Photo();
        $searchQuery->filterTags('me Ga');
        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($blogpost3->getId()), $pagingSource->getItems());
    }

    public function testSetBirthdate() {
        $user1 = SKTest_TH::createUser();
        $user1->setBirthdate(new \DateTime('-20 years'));
        $blogpost1 = SKTest_TH::createBlogpost($user1);

        $user2 = SKTest_TH::createUser();
        $user2->setBirthdate(new \DateTime('-23 years'));
        $blogpost2 = SKTest_TH::createBlogpost($user2);

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Blogpost();
        $searchQuery->filterAgeRange(18, 25);

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(2, $pagingSource->getCount());
        $this->assertContainsAll(array($blogpost1->getId(), $blogpost2->getId()), $pagingSource->getItems());

        $user1->setBirthdate(new \DateTime('-30 years'));

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($blogpost2->getId()), $pagingSource->getItems());
    }
}
