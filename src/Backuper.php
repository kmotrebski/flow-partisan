<?php

declare(strict_types=1);

namespace KoFlow;

class Backuper
{
    /**
     * @var JiraRepository $jiraRepository
     */
    private $jiraRepository;

    /**
     * @var LocalStorage $localStorage
     */
    private $localStorage;

    public function __construct(
        JiraRepository $jiraRepository,
        LocalStorage $localStorage
    ) {
        $this->jiraRepository = $jiraRepository;
        $this->localStorage = $localStorage;
    }

    public function backup(TargetIssuesList $list): BackupSummary
    {
        $summary = new BackupSummary();

        foreach ($list as $issueKey) {

            echo (string) $summary . PHP_EOL;

            $has = $this->jiraRepository->hasIssue($issueKey);

            if ($has === false) {
                $summary = $summary->addDeleted();
                continue;
            }

            $issue = $this->jiraRepository->getIssue($issueKey);
            $this->localStorage->storeIssue($issue);
            $summary = $summary->addExisting();
        }

        $stopped = $summary->stop();
        return $stopped;
    }
}
