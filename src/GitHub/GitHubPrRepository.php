<?php

declare(strict_types=1);

namespace KoFlow\GitHub;

use Github\HttpClient\Message\ResponseMediator;

class GitHubPrRepository
{
    /**
     * @var string $userOrOrg
     */
    private $userOrOrg;

    /**
     * @var string $repositoryName
     */
    private $repositoryName;

    /**
     * @var \Github\Client
     */
    private $client;

    public function __construct(
        string $token,
        string $userOrOrg,
        string $repositoryName
    ) {
        $this->client = self::createGitHubClient($token);

        $this->userOrOrg = $userOrOrg;
        $this->repositoryName = $repositoryName;
    }

    private static function createGitHubClient(
        string $token
    ): \Github\Client {
        $client = new \Github\Client();
        $client->authenticate(
            $token,
            null,
            \Github\Client::AUTH_HTTP_TOKEN
        );
        return $client;
    }

    /**
     * @param int $page
     * @return Pr[]
     * @throws \Exception
     */
    public function getPullRequestsForPage(int $page): array
    {
        $params = [
            'state' => 'closed',
            'base' => 'master',
            'page' => (string) $page,
            'per_page' => '50',
            'direction' => 'asc',
            'sort' => 'created',
        ];

        $rawPrs = $this->client->pullRequests()->all(
            $this->userOrOrg,
            $this->repositoryName,
            $params
        );

        $output = [];

        foreach ($rawPrs as $pullRequest) {
            $number = $pullRequest['number'];
            $events = $this->getEventsForIssue($number);
            $reviews = $this->getReviews($number);

            $output[] = Pr::constructFromGitHubParts(
                $pullRequest,
                $events,
                $reviews
            );
        }

        return $output;
    }

    private function getEventsForIssue(int $issueId): array
    {
        $page = 1;
        $events = [];

        while (true) {
            $eventsForPage = $this->getEventsForIssuePaginated(
                $issueId,
                $page
            );

            if (count($eventsForPage) === 0) {
                return $events;
            }

            $events = array_merge($events, $eventsForPage);

            $page++;
        }

        $fmt = 'Sth went wrong with issue=%s';
        $msg = sprintf($fmt, $issueId);
        throw new \Exception($msg);
    }

    private function getEventsForIssuePaginated(
        int $issueId,
        int $page
    ): array {

        $eventsPaginated = $this->client->issues()->events()->all(
            $this->userOrOrg,
            $this->repositoryName,
            $issueId,
            $page
        );

        return $eventsPaginated;
    }

    private function getReviews(int $number): array
    {
        $urlFmt = '/repos/%s/%s/pulls/%s/reviews';
        $url = sprintf($urlFmt, $this->userOrOrg, $this->repositoryName, $number);

        $response = $this->client->getHttpClient()->get($url);
        $data = ResponseMediator::getContent($response);

        return $data;
    }

    public function getQuota(): \stdClass
    {
        /** @var \Github\Api\RateLimit\RateLimitResource[] $rateLimits */
        $rateLimits = $this->client->api('rate_limit')->getResource('core');

        $output = [
            'limit' => $rateLimits->getLimit(),
            'remaining' => $rateLimits->getRemaining(),
        ];

        $reset = $rateLimits->getReset();
        $now = time();
        $output['tillReset'] = $reset - $now;

        return (object) $output;
    }
}
