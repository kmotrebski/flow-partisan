<?php

declare(strict_types=1);

namespace KoFlow;

use JiraRestApi\Issue\Issue;

class LocalStorage
{
    /**
     * @var string $dir
     */
    private $dir;

    /**
     * @var \DateTimeImmutable $storingTime
     */
    private $storingTime;

    public function __construct(
        string $baseDir
    ) {
        $this->dir = $baseDir;

        $utc = new \DateTimeZone('UTC');
        $this->storingTime = new \DateTimeImmutable('now', $utc);
    }

    private static function createDir(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }

        $old = umask(0);
        $result = mkdir($dir, 0777, true);
        umask($old);

        if ($result === true) {
            return;
        }

        $fmt = 'Cannot create %s directory.';
        $msg = sprintf($fmt, $dir);
        throw new \RuntimeException($msg);
    }

    public function storeIssue(Issue $issue)
    {
        umask(0);
        $finalDir = self::getFinalDir($this->dir, $this->storingTime);
        self::createDir($finalDir);

        $issueAssJson = json_encode($issue, JSON_PRETTY_PRINT);

        $finalPath = $finalDir . '/' . $issue->key . '.json';

        file_put_contents($finalPath, $issueAssJson);

    }

    private static function getFinalDir(
        string $baseDir,
        \DateTimeImmutable $time
    ): string {
        $subdir = $time->format('Ymd_His');
        $finalDir = sprintf('%s/%s', $baseDir, $subdir);
        return $finalDir;
    }
}
