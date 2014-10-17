<?php
/** @var AP_App_Cli $this */
$this->_getStreamOutput()->writeln('Creating users...');

/** @var AP_Site_Abstract $site */
$site = AP_Site_Abstract::factory();
$faker = new Faker\UniqueGenerator(Faker\Factory::create(), 10);

$mike = AP_Model_User::createStatic(array(
    'sex'       => AP_Model_User::SEX_MALE,
    'site'      => $site,
    'location'  => $london,
    'birthdate' => new DateTime('35 years ago'),
    'email'     => 'mike@example.com',
    'username'  => 'mike',
    'password'  => 'megapass',
));

$bob = AP_Model_User::createStatic(array(
    'sex'       => AP_Model_User::SEX_MALE,
    'site'      => $site,
    'location'  => $london,
    'birthdate' => new DateTime('30 years ago'),
    'email'     => 'bob@example.com',
    'username'  => 'bob',
    'password'  => 'megapass',
));
$bob->getRoles()->add(AP_Role::PREMIUMUSER);

$alice = AP_Model_User::createStatic(array(
    'sex'       => AP_Model_User::SEX_FEMALE,
    'site'      => $site,
    'location'  => $liverpool,
    'birthdate' => new DateTime('25 years ago'),
    'email'     => 'alice@example.com',
    'username'  => 'alice',
    'password'  => 'megapass',
));
$alice->getRoles()->add(AP_Role::ADMIN);

for ($i = 0; $i < 100; $i++) {
    $user = AP_Model_User::createStatic(array(
        'sex'       => array_rand($site->getSexList()),
        'site'      => $site,
        'location'  => $london,
        'birthdate' => $faker->dateTimeBetween('100 years ago', '18 years ago'),
        'email'     => $faker->email,
        'username'  => $faker->userName,
        'password'  => 'megapass',
    ));
    $user->getFriends()->add($bob);
    $user->getFriends()->add($alice);
    $user->getFriends()->add($mike);
    $user->setOnline(true);
}
