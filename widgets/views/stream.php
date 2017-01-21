<?php
use \yii\web\View;

\humhub\modules\content\assets\Stream::register($this);

$this->registerJs('var streamUrl="' . $streamUrl . '"', View::POS_BEGIN);

$jsLoadWall = "r = new Stream('#reputationStream');\n";
$wallEntryId = (int) Yii::$app->request->getQueryParam('wallEntryId');
if ($wallEntryId != "") {
    $jsLoadWall .= "r.showItem(" . $wallEntryId . ");\n";
} else {
    $jsLoadWall .= "r.showStream();\n";
}
$jsLoadWall .= "currentStream = r;\n";
$jsLoadWall .= "mainStream = r;\n";
$jsLoadWall .= "$('#btn-load-more').click(function() { currentStream.loadMore(); })\n";
$this->registerJs($jsLoadWall, View::POS_READY);

if (Yii::$app->settings->get('horImageScrollOnMobile'))
    $this->registerJs(new \yii\web\JsExpression("
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(navigator.userAgent)) {
        $('#wallStream').addClass('mobile');
    }"), View::POS_READY);

$this->registerJsVar('defaultStreamSort', 'h');
?>

<?php if ($this->context->showFilters) { ?>
    <ul class="nav nav-tabs wallFilterPanel" id="filter" style="display: none;">
        <li class=" dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo Yii::t('ContentModule.widgets_views_stream', 'Filter'); ?> <b
                    class="caret"></b></a>
            <ul class="dropdown-menu">
                <?php foreach ($filters as $filterId => $filterTitle): ?>
                    <li><a href="#" class="wallFilter" id="<?php echo $filterId; ?>"><i
                                class="fa fa-square-o"></i> <?php echo $filterTitle; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo Yii::t('ContentModule.widgets_views_stream', 'Sorting'); ?>
                <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="#" class="wallSorting" id="sorting_h"><i
                            class="fa fa-check-square-o"></i> <?php echo Yii::t('ReputationModule.widgets_views_stream', 'Hot'); ?>
                    </a></li>
                <li><a href="#" class="wallSorting" id="sorting_t"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('ReputationModule.widgets_views_stream', 'Top'); ?>
                    </a></li>
                <li><a href="#" class="wallSorting" id="sorting_n"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('ReputationModule.widgets_views_stream', 'New'); ?>
                    </a></li>
                <li><a href="#" class="wallSorting" id="sorting_r"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('ReputationModule.widgets_views_stream', 'Rising'); ?>
                    </a></li>
            </ul>
        </li>
    </ul>
<?php } ?>

<div id="wallStream">

    <!-- DIV for a normal wall stream -->
    <div class="s2_stream" style="display:none">

        <div class="s2_streamContent"></div>
        <?php echo \humhub\widgets\LoaderWidget::widget(['cssClass' => 'streamLoader']); ?>

        <div class="emptyStreamMessage">

            <div class="<?php echo $this->context->messageStreamEmptyCss; ?>">
                <div class="panel">
                    <div class="panel-body">
                        <?php echo $this->context->messageStreamEmpty; ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="emptyFilterStreamMessage">
            <div class="placeholder <?php echo $this->context->messageStreamEmptyWithFiltersCss; ?>">
                <div class="panel">
                    <div class="panel-body">
                        <?php echo $this->context->messageStreamEmptyWithFilters; ?>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- DIV for an single wall entry -->
    <div class="s2_single" style="display: none;">
        <div class="back_button_holder">
            <a href="#"
               class="singleBackLink btn btn-primary"><?php echo Yii::t('ContentModule.widgets_views_stream', 'Back to stream'); ?></a><br><br>
        </div>
        <div class="p_border"></div>

        <div class="s2_singleContent"></div>
        <div class="loader streamLoaderSingle"></div>
        <div class="test"></div>
    </div>
</div>

<!-- show "Load More" button on mobile devices -->
<div class="col-md-12 text-center visible-xs visible-sm">
    <button id="btn-load-more" class="btn btn-primary btn-lg "><?php echo Yii::t('ContentModule.widgets_views_stream', 'Load more'); ?></button>
    <br/><br/>
</div>