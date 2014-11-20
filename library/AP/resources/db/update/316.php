<?php

if (CM_Db_Db::existsIndex('sk_affiliate', 'codeProvider')) {
    CM_Db_Db::exec("ALTER TABLE `sk_affiliate` DROP INDEX `codeProvider`, ADD UNIQUE KEY `providerCode` (`provider`,`code`)");
}
