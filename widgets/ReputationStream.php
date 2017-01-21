<?php

namespace humhub\modules\reputation\widgets;

use humhub\modules\content\widgets\Stream;

class ReputationStream extends Stream {

    public function run() {
        return $this->render('stream', ['streamUrl' => $this->getStreamUrl(), 'showFilters' => $this->showFilters, 'filters' => $this->filters]);
    }
}
