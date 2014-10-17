<?php

class SK_Component_EntityList_Profile_SearchTest extends SKTest_TestCase {

    public function testGuest() {
        $query = array(
            'sex'       => array(),
            'match_sex' => array(),
            'location'  => array(),
            'age'       => array(18, 100),
            'options'   => array(),
        );
        $cmp = new SK_Component_EntityList_Profile_Search(array('query' => $query));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.SK_Component_EntityList_Profile_Search'));
    }
}
