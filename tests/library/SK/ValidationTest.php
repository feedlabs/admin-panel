<?php

class SK_ValidationTest extends SKTest_TestCase {

    public function testHostPublic() {
        $this->assertSame('www.example.com', SK_Validation::hostPublic('www.example.com'));
        $this->assertSame('192.0.32.10', SK_Validation::hostPublic('192.0.32.10'));

        $this->assertFalse(SK_Validation::hostPublic('localhost'));
        $this->assertFalse(SK_Validation::hostPublic('10.0.0.0'));
        $this->assertFalse(SK_Validation::hostPublic('10.255.0.12'));
        $this->assertFalse(SK_Validation::hostPublic('192.168.1.1'));
    }

    public function testIp() {
        $this->assertSame('130.0.0.1', SK_Validation::ip('130.0.0.1'));
        $this->assertSame('255.255.255.255', SK_Validation::ip('255.255.255.255'));
        $this->assertSame('0.0.0.0', SK_Validation::ip('0.0.0.0'));

        $this->assertFalse(SK_Validation::ip('1.1.1'));
        $this->assertFalse(SK_Validation::ip('foo'));
        $this->assertFalse(SK_Validation::ip(null));
    }
}
