<?php

class SK_Component_EntityQueryTest extends SKTest_TestCase {

    public function testEntityQuery() {
        $entityQuery = new SK_EntityQuery_EntityQuery(new SK_Params(), new CM_Frontend_Environment());
        $cmp = new SK_Component_EntityQuery(array('entityQuery' => $entityQuery, 'urlPage' => '/'));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_EntityQuery'));
        $this->assertSame(1, $page->find('select')->count());
        $this->assertContains('Sort', $page->find('.SK_Component_EntityQuery')->getText());
    }

    public function testEntityQueryPhoto() {
        $entityQuery = new SK_EntityQuery_Photo(new SK_Params(), new CM_Frontend_Environment());
        $cmp = new SK_Component_EntityQuery(array('entityQuery' => $entityQuery, 'urlPage' => '/'));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_EntityQuery'));
        $this->assertSame(3, $page->find('select')->count());
        $this->assertContains('Sex', $page->find('.SK_Component_EntityQuery')->getText());
        $this->assertContains('Added', $page->find('.SK_Component_EntityQuery')->getText());
        $this->assertContains('Sort', $page->find('.SK_Component_EntityQuery')->getText());
    }

    public function testEntityQueryBlogpost() {
        $entityQuery = new SK_EntityQuery_Blogpost(new SK_Params(), new CM_Frontend_Environment());
        $cmp = new SK_Component_EntityQuery(array('entityQuery' => $entityQuery, 'urlPage' => '/'));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_EntityQuery'));
        $this->assertSame(2, $page->find('select')->count());
        $this->assertContains('Added', $page->find('.SK_Component_EntityQuery')->getText());
        $this->assertContains('Sort', $page->find('.SK_Component_EntityQuery')->getText());
    }

    public function testEntityQueryPinboard() {
        $entityQuery = new SK_EntityQuery_Pinboard(new SK_Params(), new CM_Frontend_Environment());
        $cmp = new SK_Component_EntityQuery(array('entityQuery' => $entityQuery, 'urlPage' => '/'));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_EntityQuery'));
        $this->assertSame(2, $page->find('select')->count());
        $this->assertContains('Added', $page->find('.SK_Component_EntityQuery')->getText());
        $this->assertContains('Sort', $page->find('.SK_Component_EntityQuery')->getText());
    }

    public function testEntityQueryProfile() {
        $entityQuery = new SK_EntityQuery_Profile(new SK_Params(), new CM_Frontend_Environment());
        $cmp = new SK_Component_EntityQuery(array('entityQuery' => $entityQuery, 'urlPage' => '/'));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_EntityQuery'));
        $this->assertSame(2, $page->find('select')->count());
        $this->assertSame(2, $page->find('input[type="checkbox"]')->count());
        $this->assertContains('Sex', $page->find('.SK_Component_EntityQuery')->getText());
        $this->assertContains('Sort', $page->find('.SK_Component_EntityQuery')->getText());
    }

    public function testEntityQueryVideo() {
        $entityQuery = new SK_EntityQuery_Video(new SK_Params(), new CM_Frontend_Environment());
        $cmp = new SK_Component_EntityQuery(array('entityQuery' => $entityQuery, 'urlPage' => '/'));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_EntityQuery'));
        $this->assertSame(2, $page->find('select')->count());
        $this->assertContains('Added', $page->find('.SK_Component_EntityQuery')->getText());
        $this->assertContains('Sort', $page->find('.SK_Component_EntityQuery')->getText());
        $this->assertNotContains('Category', $page->find('.SK_Component_EntityQuery')->getText());
    }

    public function testEntityQueryVideoWithCategory() {
        $entityQuery = new SK_EntityQuery_Video(new SK_Params(), new CM_Frontend_Environment());

        $reflectionClass = new ReflectionClass('SK_EntityQuery_Video');
        $reflectionProperty = $reflectionClass->getProperty('_availableParams');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entityQuery, array(
            'sort'     => array(
                array('value' => 'created', 'label' => 'Date Added', 'default' => true),
                array('value' => 'rating', 'label' => 'Rating'),
                array('value' => 'views', 'label' => 'View Count'),
                array('value' => 'comments', 'label' => 'Comment Count'),
                array('value' => 'relevance', 'label' => 'Relevance'),
            ),
            'added'    => array(
                array('value' => 'day', 'label' => 'Today'),
                array('value' => 'week', 'label' => 'This Week', 'default' => true),
                array('value' => 'month', 'label' => 'This Month'),
                array('value' => 'year', 'label' => 'This Year'),
                array('value' => 'all', 'label' => 'All Time'),
            ),
            'category' => array(
                array('value' => 'All', 'label' => 'All', 'default' => true),
                array('value' => 'Foo', 'label' => 'Foo'),
                array('value' => 'Bar', 'label' => 'Bar'),
            ),
            'term'     => 'string',
        ));

        $cmp = new SK_Component_EntityQuery(array('entityQuery' => $entityQuery, 'urlPage' => '/'));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_EntityQuery'));
        $this->assertSame(3, $page->find('select')->count());
        $this->assertContains('Added', $page->find('.SK_Component_EntityQuery')->getText());
        $this->assertContains('Sort', $page->find('.SK_Component_EntityQuery')->getText());
        $this->assertContains('Category', $page->find('.SK_Component_EntityQuery')->getText());
    }
}
