<?php

class SK_Component_SiteAnnouncementsTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_SiteAnnouncements();

        $this->assertComponentAccessible($cmp);
    }
}
