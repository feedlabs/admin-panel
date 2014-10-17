<?php

class SK_PaymentProvider_SegpayTest extends SKTest_TestCase {

    public function testGetFormUrl() {
        $site = $this->getMockSite('CM_Site_Abstract', null, null, array('getUrl', 'getModules'));
        $site->expects($this->any())->method('getUrl')->will($this->returnValue('http://www.example.com'));
        $site->expects($this->any())->method('getModules')->will($this->returnValue(array('SK', 'CM')));

        /** @var SK_PaymentProvider_Segpay $paymentProvider */
        $paymentProvider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::SEGPAY);
        $serviceBundle = SKTest_TH::createServiceBundle();
        $paymentProvider->setProviderBundleId($serviceBundle, '321:123');
        $user = SKTest_TH::createUser();
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site));

        $urlActual = $paymentProvider->getFormUrl($user, $serviceBundle, $render, SKTest_TH::createPaymentOption());
        $regexp = '/' .
            preg_quote('https://secure2.segpay.com/billing/poset.cgi?x-eticketid=' . urlencode('321:123') . '&x-billemail=' .
                urlencode($user->getEmail()) . '&x-auth-link=' . urlencode('http://www.example.com/') .
                '&x-decl-link=' . urlencode('http://www.example.com/payment/denial'), '/') .
            '&username\=[a-z0-9]{16}&password\=[a-z0-9]{16}&lang\=en&data=' .
            urlencode(SK_PaymentProvider_Abstract::encodeData(array('user' => $user->getId(), 'serviceBundle' => $serviceBundle->getId(),
                                                                    'site' => $render->getSite()->getType())))
            . '/';
        $this->assertSame(1, preg_match($regexp, $urlActual));
    }
}
