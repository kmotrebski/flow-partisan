<?php

$importJira = false;
$importGithub = false;

require 'vendor/autoload.php';
require_once 'settings.php';

try {

    if ($importJira === true) {

        $jiraRepo = new \KoFlow\JiraRepository($credentials);
        $storage = new \KoFlow\LocalStorage('/var/ko_flow/storage/tag');

        $list = \KoFlow\TargetIssuesList::getFromTo('TAG', 4100, 4110);

        $backuper = new \KoFlow\Backuper($jiraRepo, $storage);
        $summary = $backuper->backup($list);

        echo (string) $summary . PHP_EOL;
    }

    if ($importGithub === true) {
        //todo
    }

} catch (\Throwable $e) {
    echo (string) $e;
    exit(2);
}

exit(0);
