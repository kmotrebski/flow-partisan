<?php

declare(strict_types=1);

namespace KoFlow;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

class JiraRepository
{
    /**
     * @var IssueService $issueService
     */
    private $issueService;

    public function __construct(array $credentials)
    {
        $config = new ArrayConfiguration($credentials);
        $this->issueService = new IssueService($config);
    }

    public function hasIssue(string $issueKey): bool
    {
        try {
            $issue = $this->getIssue($issueKey);
            return true;
        } catch (JiraException $e) {
            return false;
        }
    }

    public function getIssue(string $issueKey): Issue
    {
        $issueQuery = self::getIssueQuery();
        $issue = $this->issueService->get($issueKey, $issueQuery);
        return $issue;
    }

    private static function getIssueQuery()
    {
        return [
            'expand' => [
                'changelog',
                'renderedFields',
                'names',
                'schema',
                'transitions',
                'operations',
                'editmeta',
            ],
        ];
    }
}
