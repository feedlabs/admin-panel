<?php

class SK_Component_ImportantAnnouncementsTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_ImportantAnnouncements();

        $this->assertComponentAccessible($cmp);
    }
}
