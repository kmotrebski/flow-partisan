<?php

declare(strict_types=1);

namespace KoFlow\GitHub;

class Pr implements \JsonSerializable
{
    /**
     * @var \stdClass $payload
     */
    private $payload;

    public function __construct(\stdClass $stdClass)
    {
        $this->payload = $stdClass;
    }

    public static function constructFromGitHubParts(
        array $githubPr,
        array $githubEvents
    ): self {

        $githubPrCloned = self::getClone($githubPr);
        $githubEventsCloned = self::getClone($githubEvents);

        $payload = (object) [];
        $payload->githubPr = $githubPrCloned;
        $payload->githubEvents = $githubEventsCloned;

        return new self($payload);
    }

    private static function getClone($resource)
    {
        $encoded  = json_encode($resource);
        $decoded = json_decode($encoded);
        return $decoded;
    }

    public function jsonSerialize()
    {
        return $this->payload;
    }

    public function getNumber(): int
    {
        return $this->payload->githubPr->number;
    }
}
