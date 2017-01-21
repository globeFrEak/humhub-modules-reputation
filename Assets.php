<?php

/**
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation;

class Assets extends yii\web\AssetBundle {

    public $css = [''];
    public $js = [''];

    public function init() {
        $this->sourcePath = dirname(__FILE__) . '/assets';
        parent::init();
    }

}
