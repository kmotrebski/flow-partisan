<?php

declare(strict_types=1);

namespace KoFlow;

class TargetIssuesList extends \ArrayIterator
{
    public function __construct(array $list)
    {
        foreach ($list as $element) {
            self::assertString($element);
        }

        parent::__construct($list);
    }

    private static function assertString(string $list): void
    {
        //exceptionally empty
    }

    public static function getCustom(array $list): self
    {
        return new self($list);
    }

    public static function getFromTo(string $abbr, int $from, int $to): self
    {
        if (!($from <= $to)){
            $fmt = 'from=%s, to=%s';
            $msg = sprintf($fmt, $from, $to);
            throw new \InvalidArgumentException($msg);
        }

        $list = [];

        for($i = $from; $i<= $to; $i++) {
            $list[] = self::getKeyForId($abbr, $i);
        }

        return new self($list);
    }

    private static function getKeyForId(string $abbr, int $id): string
    {
        return sprintf('%s-%s', $abbr, $id);
    }

    public static function getRandom(string $abr): self
    {
        $i = 4500;
        $ids = [];

        for($x = 0; $x < 5; $x++) {

            $id = $abr . '-' . ($x + $i);

            $ids[] = $id;

            $x++;
        }

        $list = new self($ids);
        return $list;
    }
}
