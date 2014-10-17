<?php

class SK_Elasticsearch_Type_UserTest extends SKTest_TestCase {

    /** @var SK_Elasticsearch_Type_User */
    protected $_type;

    /** @var CM_Elasticsearch_Index_Cli */
    protected $_searchIndexCli;

    public function setUp() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;

        $this->_type = new SK_Elasticsearch_Type_User();
        $this->_searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $this->_searchIndexCli->create($this->_type->getIndex()->getName());
    }

    public function tearDown() {
        $this->_type->getIndex()->delete();
        SKTest_TH::clearEnv();
    }

    public function testCommentCount() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        SKTest_TH::createComment('foo', $photo);

        $user2 = SKTest_TH::createUser();
        $photo2 = SKTest_TH::createPhoto($user2);
        SKTest_TH::createComment('foo', $photo2);
        SKTest_TH::createComment('foo', $photo2);

        CM_Model_Location::createAggregation();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Profile();
        $searchQuery->filterCommentCount(2);

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $paging->getCount());
    }

    public function testRating() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        $photo->getRating()->setScore(SKTest_TH::createUser(), 1);

        $user2 = SKTest_TH::createUser();
        $photo2 = SKTest_TH::createPhoto($user2);
        $photo2->getRating()->setScore(SKTest_TH::createUser(), -1);
        $photo2->getRating()->setScore(SKTest_TH::createUser(), -1);

        $user3 = SKTest_TH::createUser();
        $photo3 = SKTest_TH::createPhoto($user3);
        $photo3->getRating()->setScore(SKTest_TH::createUser(), 1);
        $photo3->getRating()->setScore(SKTest_TH::createUser(), -1);

        CM_Model_Location::createAggregation();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Profile();
        $searchQuery->filterRatingCount(2);
        $searchQuery->filterRating(0);

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($user3->getId()), $pagingSource->getItems());
    }

    public function testPhotoCount() {
        $user = SKTest_TH::createUser();
        SKTest_TH::createPhoto($user);

        $user2 = SKTest_TH::createUser();
        SKTest_TH::createPhoto($user2);
        $photoToBeDeleted = SKTest_TH::createPhoto($user2);

        $user3 = SKTest_TH::createUser();
        SKTest_TH::createPhoto($user3);
        SKTest_TH::createPhoto($user3);
        SKTest_TH::createPhoto($user3);

        CM_Model_Location::createAggregation();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Profile();
        $searchQuery->filterPhotoCount(2);

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $userIdList = $pagingSource->getItems();
        $this->assertSame(2, count($userIdList));

        $photoToBeDeleted->delete();

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $userIdList = $pagingSource->getItems();

        $this->assertSame(1, count($userIdList));
        $this->assertSame($user3->getId(), (int) $userIdList[0]);
    }

    public function testRoles() {
        $user1 = SKTest_TH::createUser();
        $user1->getRoles()->add(2);
        $user1->getRoles()->add(3);
        $user2 = SKTest_TH::createUser();
        $user2->getRoles()->add(3);
        $user3 = SKTest_TH::createUser();

        CM_Model_Location::createAggregation();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery1 = new SK_Elasticsearch_Query_Entity_Profile();
        $searchQuery1->filterRole(3);
        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery1);
        $this->assertSame(2, $pagingSource->getCount());
        $this->assertContainsAll(array($user1->getId(), $user2->getId()), $pagingSource->getItems());

        $searchQuery2 = new SK_Elasticsearch_Query_Entity_Profile();
        $searchQuery2->filterRole(2);
        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery2);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($user1->getId()), $pagingSource->getItems());

        $searchQuery3 = new SK_Elasticsearch_Query_Entity_Profile();
        $searchQuery3->filterRoleNot(2);
        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery3);
        $this->assertSame(2, $pagingSource->getCount());
        $this->assertContainsAll(array($user2->getId(), $user3->getId()), $pagingSource->getItems());
    }

    public function testSetBirthdate() {
        $user1 = SKTest_TH::createUser();
        $user1->setBirthdate(new \DateTime('-20 years'));

        $user2 = SKTest_TH::createUser();
        $user2->setBirthdate(new \DateTime('-23 years'));

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Profile();
        $searchQuery->filterAgeRange(18, 25);

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(2, $pagingSource->getCount());
        $this->assertContainsAll(array($user1->getId(), $user2->getId()), $pagingSource->getItems());

        $user1->setBirthdate(new \DateTime('-30 years'));

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($user2->getId()), $pagingSource->getItems());
    }
}
