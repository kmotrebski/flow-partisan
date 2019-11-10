<?php

require 'vendor/autoload.php';
require_once 'credentials.php';

try {

    $jiraRepo = new \KoFlow\JiraRepository($credentials);
    $storage = new \KoFlow\LocalStorage('/var/ko_flow/storage/pt');

    $list = \KoFlow\TargetIssuesList::getFromTo('TAG', 0, 5000);

    $backuper = new \KoFlow\Backuper($jiraRepo, $storage);
    $summary = $backuper->backup($list);

    echo (string) $summary . PHP_EOL;


} catch (\Throwable $e) {
    echo (string) $e;
    exit(2);
}

exit(0);
