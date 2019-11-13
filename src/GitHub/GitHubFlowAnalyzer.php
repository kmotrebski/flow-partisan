<?php

declare(strict_types=1);

namespace KoFlow\GitHub;

class GitHubFlowAnalyzer
{
    private $cache;

    public function __construct(
        LocalGitHubCache $cache
    ) {
        $this->cache = $cache;
    }

    public function iterate(array $listOfFiles): string
    {
        $lines = [];

        foreach ($listOfFiles as $fileName) {
            $pr = $this->cache->getPr($fileName);

            if ($pr->isGoodForFlow() === false) {
                continue;
            }

            $codeReviewEntry = $pr->getFlowEntry();

            $lines[] = $codeReviewEntry->intoString();
        }

        $linesAsString = self::getLinesIntoString($lines);
        $header = FlowEntry::getHeader();

        return $header . "\n" . $linesAsString;
    }

    private static function getLinesIntoString(array $lines): string
    {
        $string = '';

        foreach ($lines as $line) {
            $string = $string  . $line . "\n";
        }

        $trimmed = trim($string);
        return $trimmed;
    }
}
