<?php

class SK_Component_WelcomeTest extends SKTest_TestCase {

    /** @var SK_Elasticsearch_Type_User */
    private $_type;

    /** @var CM_Elasticsearch_Index_Cli */
    private $_searchIndexCli;

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

    public function testGuest() {
        $params = array('user' => SKTest_TH::createUser());
        $component = new SK_Component_Welcome($params);

        $this->assertComponentNotAccessible($component);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_Welcome(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertSame(2, $page->find('.step')->count());
        $this->assertTrue($page->has('.SK_Form_PhotoUploadThumbnail'));
        $this->assertTrue($page->has('.SK_Form_ProfileEdit_Me'));
        $this->assertTrue($page->has('.SK_Component_EntityList_Filter'));
    }

    public function testFreeuserHasMatches() {
        $user = SKTest_TH::createUser(SK_User::SEX_FEMALE);
        $user->getProfile()->getFields()->set('match_sex', array(SK_User::SEX_MALE));
        $viewer = $this->_createViewer();
        $viewer->getProfile()->getFields()->set('match_sex', array(SK_User::SEX_FEMALE));

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $cmp = new SK_Component_Welcome(null, $viewer);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertSame(2, $page->find('.step')->count());
        $this->assertTrue($page->has('.SK_Form_PhotoUploadThumbnail'));
        $this->assertTrue($page->has('.SK_Form_ProfileEdit_Me'));
        $this->assertTrue($page->has('.SK_Component_EntityList_Profile_Matches'));
    }
}
