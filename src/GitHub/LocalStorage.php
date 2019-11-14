<?php

declare(strict_types=1);

namespace KoFlow\GitHub;

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

    /**
     * @var int|null $lastPrStored
     */
    private $lastPrStored;

    public function __construct(
        string $baseDir
    ) {
        $this->dir = $baseDir;

        $utc = new \DateTimeZone('UTC');
        $this->storingTime = new \DateTimeImmutable('now', $utc);

        $this->lastPrStored = null;
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

    public function storePrs(array $prs)
    {
        if (count($prs) === 0) {
            throw new \RuntimeException('No PRs');
        }

        foreach ($prs as $pr) {
            $this->storePr($pr);
        }
    }

    private function storePr(Pr $pr)
    {
        umask(0);
        $finalDir = self::getFinalDir($this->dir, $this->storingTime);
        self::createDir($finalDir);

        $asJson = json_encode($pr, JSON_PRETTY_PRINT);

        $finalPath = sprintf('%s/%s.json', $finalDir, $pr->getNumber());

        $this->lastPrStored = $pr->getNumber();

        self::saveOnDisk($finalPath, $asJson);
    }

    private static function saveOnDisk(
        string $path,
        string $contents
    ): void {
        $result = file_put_contents($path, $contents);

        $fmt = 'Problem saving to path=%s, contents=%s';
        $msg = sprintf($fmt, $path, $contents);

        if ($result === false) {
            throw new \RuntimeException($msg);
        }

        if ($result === 0) {
            throw new \RuntimeException($msg);
        }
    }

    private static function getFinalDir(
        string $baseDir,
        \DateTimeImmutable $time
    ): string {
        $subdir = $time->format('Ymd_His');
        $finalDir = sprintf('%s/%s', $baseDir, $subdir);
        return $finalDir;
    }

    /**
     * @return int
     */
    public function getLastPrStored(): int
    {
        if ($this->lastPrStored === null) {
            throw new \RuntimeException('Nothing stored!');
        }

        return $this->lastPrStored;
    }
}
