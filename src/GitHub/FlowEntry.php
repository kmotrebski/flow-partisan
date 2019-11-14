<?php

declare(strict_types=1);

namespace KoFlow\GitHub;

class FlowEntry
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    private $mergedAt;

    /**
     * @var \DateTimeImmutable
     */
    private $reviewedAt;

    /**
     * @var string $username
     */
    private $username;

    public function __construct(
        int $id,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $mergedAt,
        \DateTimeImmutable $reviewedAt,
        string $username
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->mergedAt = $mergedAt;
        $this->reviewedAt = $reviewedAt;
        $this->username = $username;
    }

    public static function getHeader(): string
    {
        return 'id,created_at,merged_at,reviewedAt,sec_from_created_to_merged,sec_from_merged_to_reviewed,username';
    }

    public function intoString(): string
    {
        $createdAtTs = (int) $this->createdAt->getTimestamp();
        $mergedAtTs = (int) $this->mergedAt->getTimestamp();
        $codeReviewTs = (int) $this->reviewedAt->getTimestamp();

        $sec_from_created_to_merged = $mergedAtTs - $createdAtTs;
        $sec_from_merged_to_reviewed = $codeReviewTs - $mergedAtTs;

        $array = [
            $this->id,
            $this->createdAt->format('Y-m-d H:i:s'),
            $this->mergedAt->format('Y-m-d H:i:s'),
            $this->reviewedAt->format('Y-m-d H:i:s'),
            $sec_from_created_to_merged,
            $sec_from_merged_to_reviewed,
            $this->username,
        ];

        return implode(',', $array);
    }


}