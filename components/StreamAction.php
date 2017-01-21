<?php

/**
 * Description of humhub\modules\reputation\components\StreamAction
 *
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation\components;

use humhub\modules\content\components\actions\ContentContainerStream;
use Yii;

class StreamAction extends ContentContainerStream {

    /**
     * Sort by reputation_content value
     * The content creation time is not used here
     */
    const MODE_HOT = 'h';

    /**
     * Sort by reputation_content value_time:
     * The content creation_time is used here
     * Posts loose 50% of their score in one week (exponential degression)
     */
    const MODE_TOP = 't';

    /**
     * Sort by reputation_content value
     * Only show posts younger than 36 hours
     */
    const MODE_NEW = 'n';

    /**
     * Sort by reputation_content value_time:
     * Similar to SORT_TOP but here degression is faster.
     * A post loses 50% of it's score in 24 hours
     */
    const MODE_RISING = 'r';

    public $contentContainer;

    public function init() {
        parent::init();
        $sort = Yii::$app->getRequest()->get('sort', self::MODE_HOT);
        if ($sort === self::MODE_HOT) {
            $this->sort = $sort;
        } elseif ($sort === self::MODE_NEW) {
            $this->sort = $sort;
        } elseif ($sort === self::MODE_TOP) {
            $this->sort = $sort;
        } elseif ($sort === self::MODE_RISING) {
            $this->sort = $sort;
        }
        $this->setupCriteria();
    }

    public function setupCriteria() {
        parent::setupCriteria();
        /**
         * Setup Sorting
         */
        if ($this->sort === self::MODE_HOT) {
            $this->activeQuery->leftJoin('reputation_content AS rc', 'rc.content_id = wall_entry.content_id');
            if ($this->from != "") {
                $params = array(':from' => $this->from);
                $this->activeQuery->andWhere('wall_entry.content_id = :from', $params);
            }
            $this->activeQuery->orderBy('rc.score_long DESC');
        } elseif ($this->sort === self::MODE_NEW) {
            $this->activeQuery->leftJoin('reputation_content AS rc', 'rc.content_id = wall_entry.content_id');
            if ($this->from != "") {
                $params = array(':from' => $this->from);
                $this->activeQuery->andWhere('wall_entry.content_id = :from', $params);
            }
            $this->activeQuery->andWhere("content.created_at >= DATE_SUB(NOW(), INTERVAL 36 HOUR)");
            $this->activeQuery->orderBy('rc.score DESC');
        } elseif ($this->sort === self::MODE_TOP) {
            $this->activeQuery->leftJoin('reputation_content AS rc', 'rc.content_id = wall_entry.content_id');
            if ($this->from != "") {
                $params = array(':from' => $this->from);
                $this->activeQuery->andWhere('wall_entry.content_id = :from', $params);
            }
            $this->activeQuery->orderBy('rc.score DESC');
        } elseif ($this->sort === self::MODE_RISING) {
            $this->activeQuery->leftJoin('reputation_content AS rc', 'rc.content_id = wall_entry.content_id');
            if ($this->from != "") {
                $params = array(':from' => $this->from);
                $this->activeQuery->andWhere('wall_entry.content_id = :from', $params);
            }
            $this->activeQuery->orderBy('rc.score_short DESC');
        }
    }

}

?>