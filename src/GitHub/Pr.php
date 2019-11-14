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
        array $githubEvents,
        array $githubReviews
    ): self {

        $githubPrCloned = self::getClone($githubPr);
        $githubEventsCloned = self::getClone($githubEvents);

        $payload = (object) [];
        $payload->githubPr = $githubPrCloned;
        $payload->githubEvents = $githubEventsCloned;
        $payload->githubReviews = $githubReviews;

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

    private function getMergedAt()
    {
        return $this->payload->githubPr->merged_at;
    }

    private function getCreatedAt()
    {
        return $this->payload->githubPr->created_at;
    }

    public function isMerged(): bool
    {
        $merged_at = $this->getMergedAt();
        if ($merged_at === null) {
            return  false;
        }

        if (is_string($merged_at)) {
            return true;
        }

        throw new \RuntimeException();
    }

    public function isCodeReviewed(): bool
    {
        $labels = $this->getLabels();
        return in_array('reviewed', $labels, true);
    }

    public function getCodeReviewDate(): \DateTimeImmutable
    {
        if ($this->isCodeReviewed() === false) {
            $msg = 'It is not code reviewed!';
            throw new \RuntimeException($msg);
        }


        $latestDate = null;

        $events = $this->payload->githubEvents;

        foreach ($events as $event) {

            if ($event->event !== 'labeled') {
                continue;
            }

            if ($event->label->name !== 'reviewed') {
                continue;
            }

            $date = self::getDateTime($event->created_at);

            if ($latestDate === null) {
                $latestDate = $date;
            }

            if ($latestDate < $date) {
                $latestDate = $date;
            }
        }

        if ($latestDate === null) {
            throw new \Exception('No date found!');
        }

        return $latestDate;
    }

    private function getLabels(): array
    {
        $labels = [];
        $labelsArray = $this->payload->githubPr->labels;

        foreach ($labelsArray as $githubLabel) {
            $labels[] = $githubLabel->name;
        }

        return $labels;
    }

    public function isGoodForFlow(): bool
    {
        if ($this->isMerged() === false) {
            return false;
        }

        if ($this->isCodeReviewed() === false) {
            return false;
        }

        return true;
    }

    public function getFlowEntry(): FlowEntry
    {
        $merged_at = $this->getMergedAt();
        $created_at = $this->getCreatedAt();

        $createdAt = self::getDateTime($created_at);
        $mergedAt = self::getDateTime($merged_at);
        $codeReviewDate = $this->getCodeReviewDate();

        return new FlowEntry(
            $this->getNumber(),
            $createdAt,
            $mergedAt,
            $codeReviewDate,
            $this->getUsername()
        );
    }

    private function getUsername(): string
    {
        return $this->payload->githubPr->user->login;
    }

    private static function getDateTime(string $asString): \DateTimeImmutable
    {
        $utc = new \DateTimeZone('UTC');
        $mergedAt = \DateTimeImmutable::createFromFormat(DATE_ISO8601, $asString, $utc);
        $mergedAt = $mergedAt->setTimezone($utc);
        return $mergedAt;
    }
}
