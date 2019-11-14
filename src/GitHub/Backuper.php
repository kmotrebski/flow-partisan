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

            $this->waitForQuota();

            $prs = $this->githubRepository->getPullRequestsForPage($page);

            if (count($prs) === 0) {
                return;
            }

            $this->localStorage->storePrs($prs);

            $lastPr = $this->localStorage->getLastPrStored();
            $this->outputProgress($page, $lastPr);

            $page++;

            $this->outputQuota();

            if ($toPage < $page) {
                return;
            }
        }
    }

    private function outputProgress(int $page, int $lastPr): void
    {
        $fmt = "Finished page %s, last PR=%s." . PHP_EOL;
        $msg = sprintf($fmt, $page, $lastPr);
        echo $msg;
    }

    private function waitForQuota(): void
    {
        $minAcceptedToContinue = 500;

        while (true) {

            $quota = $this->githubRepository->getQuota();
            $remaining = $quota->remaining;

            if ($remaining >= $minAcceptedToContinue) {
                return;
            }

            $this->outputQuota('Waiting for quota');
            sleep(60);
        }

    }

    private function outputQuota(string $msg = 'Quota'): void
    {
        $quota = $this->githubRepository->getQuota();
        $asJson = json_encode($quota);
        $fmt = '%s: %s' . PHP_EOL;
        $msg = sprintf($fmt, $msg, $asJson);
        echo $msg;
    }
}
