<?php

namespace KoFlow;

class BackupSummary
{
    /**
     * @var float $start
     */
    private $start;

    /**
     * @var float $stop
     */
    private $stop;

    /**
     * @var int $existing
     */
    private $existing;

    public function __construct() {
        $this->start = microtime(true);
        $this->stop = null;
        $this->existing = 0;
        $this->deleted = 0;
    }

    public function stop(): self
    {
        $clonned = clone $this;
        $clonned->stop = microtime(true);
        return $clonned;
    }

    public function addExisting(): self
    {
        $clonned = clone $this;
        $clonned->existing++;
        return $clonned;
    }

    public function addDeleted(): self
    {
        $clonned = clone $this;
        $clonned->deleted++;
        return $clonned;
    }

    public function __toString()
    {
        if ($this->stop === null) {
            $timeInSecs = microtime(true) - $this->start;
        } else {
            $timeInSecs = $this->stop - $this->start;
        }

        $timeInSecs = (int) $timeInSecs;

        $total = $this->existing + $this->deleted;

        if ($total !== 0) {
            $average = $timeInSecs / $total;
        } else {
            $average = $timeInSecs;
        }

        $data = (object) [
            'timeInSecs' => $timeInSecs,
            'existing' => $this->existing,
            'deleted' => $this->deleted,
            'total' => $total,
            'average' => $average,
        ];

        $asJson = json_encode($data);
        return $asJson;
    }
}
