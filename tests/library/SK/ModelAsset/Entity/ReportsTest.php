<?php

class SK_ModelAsset_Entity_ReportsTest extends SKTest_TestCase {

    public function testAdd() {
        $user = SKTest_TH::createUser();
        $reporter = SKTest_TH::createUser();

        $user->getProfile()->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, 'blub');
        $this->assertRow('sk_report', array('userId'     => $user->getId(), 'reporterId' => $reporter->getId(),
                                            'entityType' => $user->getProfile()->getType(), 'entityId' => $user->getProfile()->getId(),
                                            'reportType' => SK_ModelAsset_Entity_Reports::OTHER, 'reason' => 'blub'));

        $photo = SKTest_TH::createPhoto($user);
        $photo->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, 'blub');
        $this->assertRow('sk_report', array('userId'   => $user->getId(), 'reporterId' => $reporter->getId(), 'entityType' => $photo->getType(),
                                            'entityId' => $photo->getId(), 'reportType' => SK_ModelAsset_Entity_Reports::OTHER, 'reason' => 'blub'));

        $video = SKTest_TH::createVideo($user);
        $video->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, 'blub');
        $this->assertRow('sk_report', array('userId'   => $user->getId(), 'reporterId' => $reporter->getId(), 'entityType' => $video->getType(),
                                            'entityId' => $video->getId(), 'reportType' => SK_ModelAsset_Entity_Reports::OTHER, 'reason' => 'blub'));

        $blogpost = SKTest_TH::createBlogpost($user);
        $blogpost->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, 'blub');
        $this->assertRow('sk_report', array('userId'   => $user->getId(), 'reporterId' => $reporter->getId(), 'entityType' => $blogpost->getType(),
                                            'entityId' => $blogpost->getId(), 'reportType' => SK_ModelAsset_Entity_Reports::OTHER,
                                            'reason'   => 'blub'));
        $this->assertEquals(4, $user->getReportList()->getCount());
    }

    public function testGet() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        $reporter = SKTest_TH::createUser();
        $reporter2 = SKTest_TH::createUser();

        $user->getProfile()->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, 'blub');
        $user->getProfile()->getReports()->add(SK_ModelAsset_Entity_Reports::INAPPROPRIATE, $reporter, 'blub');
        $user->getProfile()->getReports()->add(SK_ModelAsset_Entity_Reports::WRONGSEX, $reporter2, 'blub');
        $photo->getReports()->add(SK_ModelAsset_Entity_Reports::INAPPROPRIATE, $reporter, 'blub');
        $photo->getReports()->add(SK_ModelAsset_Entity_Reports::INAPPROPRIATE, $reporter2, 'blub');
        $reports = $user->getProfile()->getReports()->get();
        foreach ($reports as $report) {
            $this->assertEquals('blub', $report['reason']);
        }
        $this->assertEquals(2, $reports->getCount());
        $this->assertEquals(4, $user->getReportList()->getCount());
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        $reporter = SKTest_TH::createUser();
        $reporter2 = SKTest_TH::createUser();

        $user->getProfile()->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, 'blub');
        $user->getProfile()->getReports()->add(SK_ModelAsset_Entity_Reports::INAPPROPRIATE, $reporter2, 'blob');
        $photo->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, 'blub');
        $user->getProfile()->getReports()->add(SK_ModelAsset_Entity_Reports::WRONGSEX, $reporter, 'blab');
        $reports = $user->getProfile()->getReports()->get();
        $this->assertEquals(2, $reports->getCount());
        $this->assertEquals(3, $user->getReportList()->getCount());
        $arr = $reports->getItems();
        $user->getProfile()->getReports()->delete($arr[0]['id']);

        $reports = $user->getProfile()->getReports()->get();
        $this->assertEquals(1, $reports->getCount());
        foreach ($reports as $report) {
            $this->assertNotEquals($arr[0]['reason'], $report['reason']);
        }
    }

    public function testEmptyReason() {
        $profile = SKTest_TH::createUser()->getProfile();
        $reporter = SKTest_TH::createUser();
        $profile->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, '');
        $report = $profile->getReports()->get()->getItem(0);

        $this->assertNull($report['reason']);
    }

    public function testDuplicateReport() {
        $profile = SKTest_TH::createUser()->getProfile();
        $reporter = SKTest_TH::createUser();
        $profile->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, 'blub');
        $reports = $profile->getReports()->get();
        $this->assertEquals(1, $reports->getCount());
        $report = $profile->getReports()->get()->getItem(0);
        $this->assertEquals('blub', $report['reason']);
        $profile->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter, 'blab');
        $reports = $profile->getReports()->get();
        $this->assertEquals(1, $reports->getCount());
        $report = $profile->getReports()->get()->getItem(0);
        $this->assertEquals('blab', $report['reason']);
    }

    public function testDeleteAll() {
        $photo = SKTest_TH::createPhoto();
        $reporter1 = SKTest_TH::createUser();
        $reporter2 = SKTest_TH::createUser();
        $photo->getReports()->add(SK_ModelAsset_Entity_Reports::INAPPROPRIATE, $reporter1);
        $photo->getReports()->add(SK_ModelAsset_Entity_Reports::INAPPROPRIATE, $reporter2);
        $this->assertEquals(2, $photo->getReports()->get()->getCount());

        $reporter1->delete();
        $this->assertEquals(1, $photo->getReports()->get()->getCount());
    }

    public function testExists() {
        $photo = SKTest_TH::createPhoto();
        $reporter1 = SKTest_TH::createUser();
        $reporter2 = SKTest_TH::createUser();

        $this->assertFalse($photo->getReports()->exists($reporter1));
        $this->assertFalse($photo->getReports()->exists($reporter2));

        $photo->getReports()->add(SK_ModelAsset_Entity_Reports::SPAM, $reporter1);
        $this->assertTrue($photo->getReports()->exists($reporter1));
        $this->assertFalse($photo->getReports()->exists($reporter2));
    }
}
