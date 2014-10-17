<?php

/** @var AP_App_Cli $this */
$this->_getStreamOutput()->writeln('Creating feed...');

$faker = Faker\Factory::create();
$imageList = $this->_getExampleImages();
$userList = new AP_Paging_User_Friends($bob);

$entityTypes = array_flip(['status', 'blogpost', 'photo']);
if (0 === count($imageList)) {
    unset($entityTypes['photo']);
    $this->_getStreamOutput()->writeln('Warning: No images present - photo entities will be skipped');
}

for ($i = 0; $i < 300; $i++) {
    /** @var AP_Model_User $user */
    $user = $userList->getItemRand();

    $entityType = array_rand($entityTypes);
    switch ($entityType) {
        case 'status':
            $entity = AP_Entity_Status::create($user, $faker->sentence());
            break;

        case 'blogpost':
            $paragraphsCount = rand(1, 5);
            $entity = AP_Entity_Blogpost::create($faker->sentence(), $faker->paragraphs($paragraphsCount, true), $user, AP_ModelAsset_Entity_PrivacyAbstract::NONE);
            break;

        case 'photo':
            $image = $imageList[array_rand($imageList)];
            $entity = AP_Entity_Photo::create($image, $user, $faker->sentence());
            if (0 === rand(0, 3)) {
                $user->setThumbnail($entity);
            }
            break;
        default:
            throw new CM_Exception_Invalid("Invalid entity type `{$entityType}`");
    }
    $commentsCount = rand(0, 4);
    for ($j = 0; $j < $commentsCount; $j++) {
        $commenter = $userList->getItemRand();
        $sentencesCount = rand(1, 3);
        AP_Entity_Comment::create($commenter, $entity, $faker->sentences($sentencesCount, true));
    }
    $user->getFeedEntryList()->add($entity);
}
