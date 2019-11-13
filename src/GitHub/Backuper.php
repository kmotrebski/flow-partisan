<?php

declare(strict_types=1);

namespace KoFlow\GitHub;

class Backuper
{
    /**
     * @var GitHubPrRepository $githubRepository
     */
    private $githubRepository;

    /**
     * @var LocalStorage $localStorage
     */
    private $localStorage;

    public function __construct(
        GitHubPrRepository $githubRepository,
        LocalStorage $localStorage
    ) {
        $this->githubRepository = $githubRepository;
        $this->localStorage = $localStorage;
    }

    public function backup(int $fromPage, int $toPage)
    {
        $page = $fromPage;

        while (true) {

            $prs = $this->githubRepository->getPullRequestsForPage($page);

            if (count($prs) === 0) {
                return;
            }

            $this->localStorage->storePrs($prs);

            $page++;

            $this->outputQuota();

            if ($toPage < $page) {
                return;
            }
        }
    }

    private function outputQuota(): void
    {
        $quota = $this->githubRepository->getQuota();
        $asJson = json_encode($quota);
        echo $asJson . PHP_EOL;
    }
}
