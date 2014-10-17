<?php

class SK_Component_ReviewList_UserTest extends SKTest_TestCase {

    public function testReviewer() {
        $viewer = $this->_createViewer(SK_Role::REVIEWER);
        $reviewerAdmin = $this->_createViewer(SK_Role::ADMIN);
        $reviewerModerator = $this->_createViewer(SK_Role::MODERATOR);
        $user = SKTest_TH::createUser();
        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $reviewerAdmin);
        SKTest_TH::timeForward(1);
        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_LEGIT, $reviewerModerator);
        $cmp = new SK_Component_ReviewList_User(array('user' => $user));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);

        $this->assertSame(4, $page->find('.dataTable tr:eq(1) td')->count());
        $this->assertSame('.internals.asset.review.1', $page->find('.dataTable tr:eq(1) td:eq(0)')->getText());
        $this->assertSame('', trim($page->find('.dataTable tr:eq(1) td:eq(1)')->getText()));
        $this->assertSame($reviewerModerator->getDisplayName(), $page->find('.dataTable tr:eq(1) td:eq(2)')->getText());
        $this->assertSame('.date.timeago.prefixAgo .date.timeago.seconds .date.timeago.suffixAgo', $page->find('.dataTable tr:eq(1) td:eq(3)')->getText());

        $this->assertSame(4, $page->find('.dataTable tr:eq(2) td')->count());
        $this->assertSame('.internals.asset.review.2', $page->find('.dataTable tr:eq(2) td:eq(0)')->getText());
        $this->assertSame('', trim($page->find('.dataTable tr:eq(2) td:eq(1)')->getText()));
        $this->assertSame($reviewerAdmin->getDisplayName(), $page->find('.dataTable tr:eq(2) td:eq(2)')->getText());
        $this->assertSame('.date.timeago.prefixAgo .date.timeago.seconds .date.timeago.suffixAgo', $page->find('.dataTable tr:eq(2) td:eq(3)')->getText());
    }

    public function testModerator() {
        $viewer = $this->_createViewer(SK_Role::MODERATOR);
        $reviewer = $this->_createViewer(SK_Role::ADMIN);
        $user = SKTest_TH::createUser();
        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $reviewer);
        $cmp = new SK_Component_ReviewList_User(array('user' => $user));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertSame(3, $page->find('.dataTable tr:eq(1) td')->count());
        $this->assertSame('.internals.asset.review.2', $page->find('.dataTable tr:eq(1) td:eq(0)')->getText());
        $this->assertSame('', trim($page->find('.dataTable tr:eq(1) td:eq(1)')->getText()));
        $this->assertSame('.date.timeago.prefixAgo .date.timeago.seconds .date.timeago.suffixAgo', $page->find('.dataTable tr:eq(1) td:eq(2)')->getText());
    }
}
