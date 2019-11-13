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

    public function backup()
    {
        $page = 1;

        while (true) {
            $prs = $this->githubRepository->getPullRequestsForPage($page);

            if (count($prs) === 0) {
                return;
            }

            $this->localStorage->storePrs($prs);

            $page++;

            if ($page > 3) {
                return;
            }
        }
    }
}
