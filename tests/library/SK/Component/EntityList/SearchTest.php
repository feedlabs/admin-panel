<?php

class SK_Component_EntityList_SearchTest extends SKTest_TestCase {

    /** @var CM_Elasticsearch_Index_Cli */
    private $_searchIndexCli;

    public function setUp() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;

        SKTest_TH::clearDb();
        $this->_searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $this->_searchIndexCli->create();
    }

    public function tearDown() {
        $this->_searchIndexCli->delete();
    }

    public function testPaging() {
        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo'));
        $html = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertSame(0, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());

        SKTest_TH::createUser(null, 'foo');
        SKTest_TH::createUser(null, 'foo2');
        $photo = SKTest_TH::createPhoto();
        $photo->getTags()->add('foo');
        $video = SKTest_TH::createVideo();
        $video->getTags()->add('foo');
        $blogpost = SKTest_TH::createBlogpost();
        $blogpost->getTags()->add('foo');
        $pinboard = SKTest_TH::createPinboard(null, 'foo');
        $pinboard->add($photo);
        $pinboard->add($video);
        $pinboard->add($blogpost);

        SKTest_TH::clearCache();
        $this->_searchIndexCli->update();

        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo'));
        $html = $this->_renderComponent($cmp);
        $this->assertSame(5, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());

        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo', 'type' => 'profile'));
        $html = $this->_renderComponent($cmp);
        $this->assertSame(2, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());

        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo', 'type' => 'photo'));
        $html = $this->_renderComponent($cmp);
        $this->assertSame(1, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());

        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo', 'type' => 'video'));
        $html = $this->_renderComponent($cmp);
        $this->assertSame(1, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());

        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo', 'type' => 'blogpost'));
        $html = $this->_renderComponent($cmp);
        $this->assertSame(1, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());

        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo', 'type' => 'pinboard'));
        $html = $this->_renderComponent($cmp);
        $this->assertSame(1, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());
    }

    public function testCensored() {
        SKTest_TH::createUser(null, 'foo');
        SKTest_TH::createUser(null, 'bar');

        SKTest_TH::clearCache();
        $this->_searchIndexCli->update();

        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo bar'));
        $html = $this->_renderComponent($cmp);
        $this->assertSame(2, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());

        $badWords = new CM_Paging_ContentList_Badwords();
        $badWords->add('foo');

        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo bar'));
        $html = $this->_renderComponent($cmp);
        $this->assertSame(1, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());

        $badWords->add('bar');

        $cmp = new SK_Component_EntityList_Search(array('term' => 'foo bar'));
        $html = $this->_renderComponent($cmp);
        $this->assertSame(0, $html->find('.SK_Component_EntityList_Search .entityList > .entityListItem')->count());
    }
}
