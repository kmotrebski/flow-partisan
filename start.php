<?php

set_error_handler(function ($severity, $message, $file, $line) {
    throw new \ErrorException($message, $severity, $severity, $file, $line);
});

$importJira = false;
$importGithub = false;
$analyzeGitHub = false;

$githubToken = null;
$githubUser = null;
$githubRepository = null;

require 'vendor/autoload.php';
require_once 'settings.php';

// is:closed base:master sort:created-asc
// is:pr is:merged label:reviewed

try {

    if ($importJira === true) {

        $jiraRepo = new \KoFlow\JiraRepository($credentials);
        $storage = new \KoFlow\LocalStorage('/var/ko_flow/storage/tag');

        $list = \KoFlow\TargetIssuesList::getFromTo('TAG', 4100, 4110);

        $backuper = new \KoFlow\Backuper($jiraRepo, $storage);
        $summary = $backuper->backup($list);

        echo (string) $summary . PHP_EOL;
        exit(0);
    }

    if ($importGithub === true) {

        $prRepo = new \KoFlow\GitHub\GitHubPrRepository(
            $githubToken,
            $githubUser,
            $githubRepository
        );

        $prStorage = new \KoFlow\GitHub\LocalStorage('/var/ko_flow/storage/github');

        $backuper = new \KoFlow\GitHub\Backuper($prRepo, $prStorage);
        $backuper->backup(1, 3);

        exit(0);
    }

    if ($analyzeGitHub === true) {
        $path = '/var/ko_flow/storage/github/20191111_113654/';
        $localGitHubCache = new \KoFlow\GitHub\LocalGitHubCache($path);
        $fileList = $localGitHubCache->getListOfFiles();
        $githubFlowAnalyzer = new \KoFlow\GitHub\GitHubFlowAnalyzer($localGitHubCache);
        $csvFile = $githubFlowAnalyzer->iterate($fileList);
        file_put_contents('/var/ko_flow/storage/flow.csv', $csvFile);
    }


} catch (\Throwable $e) {
    echo (string) $e;
    exit(2);
}

exit(0);
