<?php

/** @var AP_App_Cli $this */
$this->_getStreamOutput()->writeln('Creating conversations...');
$faker = \Faker\Factory::create();
/** @var AP_Model_User[] $userList */
$userList = [
    AP_Model_User::findUsername('bob'),
    AP_Model_User::findUsername('alice'),
    AP_Model_User::findUsername('mike'),
];

foreach ($userList as $user) {
    $friends = $user->getFriends();
    for ($i = 0; $i <= 20; $i++) {
        $recipients = $friends->getItems($i, rand(1, 3));
        $conversation = $user->getConversations()->add($recipients);

        $messagesCount = rand(3, 10);
        if (20 === $i) {
            $messagesCount = 300;
        }
        for ($j = 0; $j < $messagesCount; $j++) {
            /** @var AP_Model_User $author */
            $author = $conversation->getUsers()->getItemRand();
            $sentencesCount = rand(1, 5);
            $conversation->getMessages()->addMessageText($author, $faker->sentences($sentencesCount, true));
        }
    }
}
