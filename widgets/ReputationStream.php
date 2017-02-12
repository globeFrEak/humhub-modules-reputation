<?php

namespace humhub\modules\reputation\widgets;

use humhub\modules\stream\widgets\StreamViewer;

class ReputationStream extends StreamViewer {

    /**
     * @inheritdoc
     */
    protected function getStreamUrl() {
        $params = array_merge([
            'mode' => \humhub\modules\reputation\components\StreamAction::MODE_HOT
                ], $this->streamActionParams);

        if ($this->contentContainer) {
            return $this->contentContainer->createUrl($this->streamAction, $params);
        } else {
            array_unshift($params, $this->streamAction);
            return Url::to($params);
        }
    }

}
