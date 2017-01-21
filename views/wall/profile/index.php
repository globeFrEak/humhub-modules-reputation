<?php

/**
 * View to list all categories and their links.
 *
 * @uses $categories an array of the categories to show.
 * @uses $links an array of arrays of the links to show, indicated by the category id.
 * @uses $accesslevel the access level of the user currently logged in.
 *
 * @author Sebastian Stumpf
 */
use yii\helpers\Html;
use yii\helpers\Url;

//humhub\modules\linklist\Assets::register($this);
?>

<div id="linklist-empty-txt" <?php
if (empty($categories)) {
    echo 'style="visibility:visible; display:block"';
}
?>>
<?php echo Yii::t('ReputationModule.base', 'There have been no links or categories added to this space yet.') ?> <i class="fa fa-frown-o"></i></div>

<div class="linklist-categories">
    <?php foreach ($categories as $category) { ?>
        <div id="linklist-category_<?php echo $category->id ?>"
             class="panel panel-default panel-linklist-category" data-id="<?php echo $category->id ?>">
            <div class="panel-heading">
                <div class="heading">
                    <?php echo Html::encode($category->title); ?>
                    <?php if ($accessLevel != 0) { ?>
                        <div class="linklist-edit-controls linklist-editable">
                            <?php
                            if ($accessLevel == 2) {
                                // admins may edit and delete categories
                                echo \humhub\widgets\ModalConfirm::widget(array(
                                    'uniqueID' => 'modal_categorydelete_' . $category->id,
                                    'linkOutput' => 'a',
                                    'class' => 'deleteButton btn btn-xs btn-danger" title="' . Yii::t('LinklistModule.base', 'Delete category'),
                                    'title' => Yii::t('LinklistModule.base', '<strong>Confirm</strong> category deleting'),
                                    'message' => Yii::t('LinklistModule.base', 'Do you really want to delete this category? All connected links will be lost!'),
                                    'buttonTrue' => Yii::t('LinklistModule.base', 'Delete'),
                                    'buttonFalse' => Yii::t('LinklistModule.base', 'Cancel'),
                                    'linkContent' => '<i class="fa fa-trash-o"></i>',
                                    'linkHref' => $contentContainer->createUrl("/linklist/linklist/delete-category", array('category_id' => $category->id)),
                                    'confirmJS' => 'function() {
								$("#linklist-category_' . $category->id . '").remove();
								$("#linklist-widget-category_' . $category->id . '").remove();
								if($(".panel-linklist-widget").find(".media").length == 0) {
									$(".panel-linklist-widget").remove();
								}
							}'
                                ));
                                echo Html::a('<i class="fa fa-pencil"></i>', $contentContainer->createUrl('/linklist/linklist/edit-category', ['category_id' => $category->id]), array('title' => 'Edit Category', 'class' => 'btn btn-xs btn-primary')) . ' ';
                            }
                            // all users may add a link to an existing category
                            echo Html::a('<i class="fa fa-plus" style="font-size: 12px;"></i> Add link', $contentContainer->createUrl('/linklist/linklist/edit-link', ['link_id' => -1, 'category_id' => $category->id]), array('title' => 'Add Link', 'class' => 'btn btn-xs btn-info'));
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="media">
                    <?php if (!($category->description == NULL || $category->description == "")) { ?>
                        <div class="media-heading"><?php echo Html::encode($category->description); ?></div>
                    <?php } ?>
                    <div class="media-body">
                        <ul class="linklist-links">
                            <?php foreach ($links[$category->id] as $link) { ?>
                                <li class="linklist-link" id="linklist-link_<?php echo $link->id; ?>"
                                    data-id="<?php echo $link->id; ?>">
                                    <a href="<?php echo $link->href; ?>" target="_blank"
                                       title=""><span class="title"><?php echo Html::encode($link->title); ?></span><div class="link-description"><?php echo Html::encode($link->description); ?></div></a>

                                    <div class="linklist-interaction-controls">
                                        <?php echo \humhub\modules\comment\widgets\CommentLink::widget(array('object' => $link, 'mode' => 'popup')); ?> &middot;
                                        <?php echo humhub\modules\like\widgets\LikeLink::widget(array('object' => $link)); ?>
                                    </div>
                                    <?php // all admins and users that created the link may edit or delete it  ?>
                                    <?php if ($accessLevel == 2 || $accessLevel == 1 && $link->content->created_by == Yii::$app->user->id) { ?>
                                        <div class="linklist-edit-controls linklist-editable">
                                            <?php
                                            echo \humhub\widgets\ModalConfirm::widget(array(
                                                'uniqueID' => 'modal_linkdelete_' . $link->id,
                                                'linkOutput' => 'a',
                                                'class' => 'deleteButton btn btn-xs btn-danger" title="' . Yii::t('LinklistModule.base', 'Delete link'),
                                                'title' => Yii::t('LinklistModule.base', '<strong>Confirm</strong> link deleting'),
                                                'message' => Yii::t('LinklistModule.base', 'Do you really want to delete this link?'),
                                                'buttonTrue' => Yii::t('LinklistModule.base', 'Delete'),
                                                'buttonFalse' => Yii::t('LinklistModule.base', 'Cancel'),
                                                'linkContent' => '<i class="fa fa-trash-o"></i>',
                                                'linkHref' => $contentContainer->createUrl("/linklist/linklist/delete-link", array('category_id' => $category->id, 'link_id' => $link->id)),
                                                'confirmJS' => 'function() {
											$("#linklist-link_' . $link->id . '").remove();
											$("#linklist-widget-link_' . $link->id . '").remove();
											if($("#linklist-widget-category_' . $category->id . '").find("li").length == 0) {
												$("#linklist-widget-category_' . $category->id . '").remove();
											}
											if($(".panel-linklist-widget").find(".media").length == 0) {
												$(".panel-linklist-widget").remove();
											}
										}'
                                            ));
                                            echo Html::a('<i class="fa fa-pencil"></i>', $contentContainer->createUrl('/linklist/linklist/edit-link', ['link_id' => $link->id, 'category_id' => $category->id]), array('title' => 'Edit Link', 'class' => 'btn btn-xs btn-primary')) . ' ';
                                            ?>
                                        </div>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php if ($accessLevel != 0) { ?>
    <?php
    if ($accessLevel == 2) {
        ?>
        <div
            class="linklist-add-category linklist-editable"><?php echo Html::a('Add Category', $contentContainer->createUrl('/linklist/linklist/edit-category', ['category_id' => -1]), array('class' => 'btn btn-primary')); ?></div>
    <?php } ?>
<?php } ?>