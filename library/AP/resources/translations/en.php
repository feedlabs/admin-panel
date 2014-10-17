<?php

return function (CM_Model_Language $language) {
    $language->setTranslation('The content you tried to interact with has been deleted.', 'The content you tried to interact with has been deleted.');
    $language->setTranslation('Attention: Be aware of fake support and billing representatives and report all suspicious profiles.', 'Attention: Be aware of fake support and billing representatives and report all suspicious profiles.');
    $language->setTranslation('{$user} has just sent you a message!', '{$user} has just sent you a message!', array('user'));
    $language->setTranslation('Error', 'Error');
    $language->setTranslation('{$user} has joined video chat', '{$user} has joined video chat', array('user'));
    $language->setTranslation('{$user} sent you a message. Get a <a href="{$urlPremium}">Premium Account</a> to read it!', '{$user} sent you a message. Get a <a href="{$urlPremium}">Premium Account</a> to read it!', array('user'));
    $language->setTranslation('{$user} has joined this chat', '{$user} has joined this chat', array('user'));
    $language->setTranslation('{$user} has left this chat', '{$user} has left this chat', array('user'));
    $language->setTranslation('{$user} has left video chat', '{$user} has left video chat', array('user'));
    $language->setTranslation('Add to Pinboard', 'Add to Pinboard');
    $language->setTranslation('Add to {$pinboard}', 'Add to {$pinboard}', array('pinboard'));
    $language->setTranslation('{$user} wants to be your friend.', '{$user} wants to be your friend.', array('user'));
    $language->setTranslation('{$user} accepted your friend request.', '{$user} accepted your friend request.', array('user'));
    $language->setTranslation('{$user} sent you a message.', '{$user} sent you a message.', array('user'));
    $language->setTranslation('{$user} just viewed your profile.', '{$user} just viewed your profile.', array('user'));
    $language->setTranslation('Deleted Member');
    $language->setTranslation('Online');
    $language->setTranslation('Cupid');
};
