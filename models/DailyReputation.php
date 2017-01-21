<?php
namespace humhub\modules\reputation\models;
/*
 * Keep track of daily limit for user reputation
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

class DailyReputation
{
    private $score;
    private $daily_limit;

    /**
     * @param $score : Initial score
     * @param $daily_limit : When 0 set to int_max.
     */
    public function __construct($score, $daily_limit)
    {
        if ($daily_limit == 0) {
            $this->daily_limit = PHP_INT_MAX;
        } else {
            $this->daily_limit = $daily_limit;
        }


        if ($score <= $daily_limit) {
            $this->score = $score;
        } else {
            $this->score = $daily_limit;
        }
    }

    /**
     * @return int: current score
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param $score : score to add
     */
    public function addScore($score)
    {
        if ($this->score < $this->daily_limit) {
            $this->score += $score;
        }

        if ($this->score > $this->daily_limit) {
            $this->score = $this->daily_limit;
        }
    }
}