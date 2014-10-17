<?php

class SK_Component_CoinPurchaseTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_CoinPurchase(array('purchasable' => new SK_PurchasableMock(0)));
        $this->assertComponentAccessible($cmp);
    }

    public function testRender() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_CoinPurchase(array('purchasable' => new SK_PurchasableMock(0)));
        $html = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($html->has('.purchaseItem'));
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage already purchased
     */
    public function testPurchaseTwice() {
        $viewer = $this->_createViewer();
        SKTest_TH::createCoinTransactionAdminGive($viewer, null, 100);
        $purchasable = new SK_PurchasableMock(0);
        $purchasable->purchase($viewer);
        $cmp = new SK_Component_CoinPurchase(array('purchasable' => $purchasable));
        $this->_renderComponent($cmp, $viewer);
    }
}

class SK_PurchasableMock extends CM_Class_Abstract implements SK_Purchasable {

    protected $_userList = array();

    public function getPurchasePrice(SK_User $user = null) {
        return 100;
    }

    public function isPurchased(SK_User $user = null) {
        return in_array($user->getId(), $this->_userList);
    }

    public function purchase(SK_User $user) {
        $this->_userList[] = $user->getId();
    }
}
