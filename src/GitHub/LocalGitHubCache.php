<?php

declare(strict_types=1);

namespace KoFlow\GitHub;

class LocalGitHubCache
{

    /**
     * @var string $dir
     */
    private $dir;

    public function __construct(
        string $dir
    ) {
        $this->dir = $dir;
    }

    /**
     * @return string[]
     */
    public function getListOfFiles(): array
    {
        $list = [];

        $dir = new \DirectoryIterator($this->dir);

        foreach ($dir as $fileinfo) {

            if ($fileinfo->isDot()) {
                continue;
            }

            $list[] = $fileinfo->getFilename();
        }

        return $list;
    }

    public function getPr(string $filename): Pr
    {
        $pathToFile = sprintf('%s/%s', $this->dir, $filename);
        $contents = file_get_contents($pathToFile);
        $jsonDecoded = json_decode($contents);
        $pr = new Pr($jsonDecoded);
        return $pr;
    }
}
