<?php

class SK_Component_EntityList_FilterTest extends SKTest_TestCase {

    /** @var SK_Elasticsearch_Type_Photo */
    private $_type;

    /** @var CM_Elasticsearch_Index_Cli */
    private $_searchIndexCli;

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

    public function testGuestNonPublic() {
        SKTest_TH::createPhoto();
        SKTest_TH::createPhoto();
        $photo = SKTest_TH::createPhoto();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());
        $cmp = new SK_Component_EntityList_Filter(array('type' => SK_EntityQuery_EntityQuery::getTypeString(SK_Entity_Photo::getTypeStatic())));
        $html = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($html->has('.SK_Component_EntityList_Filter'));
        $this->assertSame(3, $html->find('.SK_Component_EntityList_Filter .entityListItem')->count());

        $photo->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY);
        SKTest_TH::clearCache();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());
        $html = $this->_renderComponent($cmp);

        $this->assertSame(2, $html->find('.SK_Component_EntityList_Filter .entityListItem')->count());
    }

    public function testGuestUserBlocked() {
        $user = SKTest_TH::createUser();
        SKTest_TH::createPhoto();
        SKTest_TH::createPhoto();
        SKTest_TH::createPhoto($user);
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());
        $cmp = new SK_Component_EntityList_Filter(array('type' => SK_EntityQuery_EntityQuery::getTypeString(SK_Entity_Photo::getTypeStatic())));
        $html = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($html->has('.SK_Component_EntityList_Filter'));
        $this->assertSame(3, $html->find('.SK_Component_EntityList_Filter .entityListItem')->count());

        $user->setBlocked();
        SKTest_TH::clearCache();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());
        $html = $this->_renderComponent($cmp);

        $this->assertSame(2, $html->find('.SK_Component_EntityList_Filter .entityListItem')->count());
    }

    public function testFilterByEnvironment() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();

        SKTest_TH::createPhoto($user1);
        SKTest_TH::createPhoto($user2);

        $siteMock = $this->getMockSite('SK_Site_Abstract', null, null, array('applyEntityFilters'));
        $siteMock->expects($this->any())->method('applyEntityFilters')->will($this->returnCallback(
            function (SK_Elasticsearch_Query_Entity $searchQuery) {
                $searchQuery->filterRoleNot(SK_Role::DEVELOPER);
            })
        );
        /** @var Sk_Site_Abstract $siteMock */

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());
        $cmp = new SK_Component_EntityList_Filter(array('type' => SK_EntityQuery_EntityQuery::getTypeString(SK_Entity_Photo::getTypeStatic())));
        $html = $this->_renderComponent($cmp, null, $siteMock);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($html->has('.SK_Component_EntityList_Filter'));
        $this->assertSame(2, $html->find('.SK_Component_EntityList_Filter .entityListItem')->count());

        $user1->getRoles()->add(SK_Role::DEVELOPER);
        SKTest_TH::clearCache();
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());
        $html = $this->_renderComponent($cmp, null, $siteMock);

        $this->assertSame(1, $html->find('.SK_Component_EntityList_Filter .entityListItem')->count());
    }
}
