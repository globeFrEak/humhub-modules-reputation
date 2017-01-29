<?php

/**
 * Description of humhub\modules\reputation\models\ReputationBase
 *
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation\models;

use Yii;
use humhub\modules\reputation\models\ReputationUser;
use humhub\modules\reputation\models\ReputationContent;
use humhub\modules\content\models\Content;
use humhub\modules\comment\models\Comment;
use humhub\modules\like\models\Like;

/**
 * Base class for reputation models
 * @author Anton Kurnitzky
 */
class ReputationBase extends \humhub\components\ActiveRecord {

    // Default caching time is 15 minutes
    const CACHE_TIME_SECONDS = 900;
    // Default space reputation settings
    const LOGARITHMIC = 0;
    const LINEAR = 1;
    const DEFAULT_FUNCTION = ReputationUser::LOGARITHMIC;
    const DEFAULT_LOGARITHM_BASE = '100';
    const DEFAULT_CREATE_CONTENT = '1';
    const DEFAULT_SMB_LIKES_CONTENT = '2';
    const DEFAULT_SMB_FAVORITES_CONTENT = '2';
    const DEFAULT_SMB_COMMENTS_CONTENT = '3';
    const DEFAULT_DAILY_LIMIT = '15';
    const DEFAULT_DECREASE_WEIGHTING = '1';
    const DEFAULT_CRON_JOB = '1';
    const DEFAULT_LAMBDA_SHORT = '0.00120338';
    const DEFAULT_LAMBDA_LONG = '0.000024558786159';
    const DEFAULT_RANKING_NEW_PERIOD = '36';

    /**
     * Returns all content objects (posts, polls, etc.) from this space
     *
     * @param $spaceId : The id of the space
     * @param bool $forceUpdate : Ignore cache
     * @return Content[]
     */
    public function getContentFromSpace($spaceId, $forceUpdate = false) {

        $cacheId = 'posts_created_cache' . '_' . $spaceId;

        $spaceContent = Yii::$app->cache->get($cacheId);

        if ($spaceContent === false || $forceUpdate === true) {

            $condition = 'contentcontainer_id=:spaceId AND object_model!=:activity';
            $params = [':spaceId' => $spaceId, ':activity' => 'humhub\modules\activity\models\Activity'];
            $query = Content::find()
                    ->where($condition, $params)
                    ->all();

            Yii::$app->cache->set($cacheId, $spaceContent = $query, ReputationContent::CACHE_TIME_SECONDS);
        }

        return $spaceContent;
    }

    /**
     * Count all comments a content object has received.
     *
     * @param Content $content : The content object
     * @param $userId : The user id
     * @param $cacheId : The cache id
     * @param bool $countOwnComments : Count comments created by same user as content
     * @param bool $forceUpdate : true if cache should be ignored
     * @return Comment[]
     */
    public function getCommentsFromContent(Content $content, $userId, $cacheId, $countOwnComments = false, $forceUpdate = false) {
        $comments = Yii::$app->cache->get($cacheId);

        if ($comments === false || $forceUpdate === true) {
            $object = $content->object_model;
            $objectModel = $object::tableName();
            $comments = array();
            try {
                $query = Comment::find();
                $query->leftJoin($objectModel . ' AS o', 'comment.object_id = o.id');
                $query->leftJoin('content AS ct', 'o.id = ct.object_id');

                if ($countOwnComments === true) {
                    $condition = 'ct.id=:contentId AND ct.created_by=:userId AND comment.object_model=ct.object_model';
                } else {
                    $condition = 'ct.id=:contentId AND ct.created_by=:userId AND comment.created_by!=:userId AND comment.object_model=ct.object_model';
                }
                $params = array(':contentId' => $content->id, ':userId' => $userId);
                $query->where($condition, $params);
                $comments = $query->all();

                Yii::$app->cache->set($cacheId, $query, ReputationBase::CACHE_TIME_SECONDS);
            } catch (Exception $e) {
                Yii::trace('Couldn\'t count comments from object model: ' . $objectModel);
            }
        }

        return $comments;
    }

    /**
     * Return an array with all space settings
     * @param $space
     * @return array
     */
    protected function getSpaceSettings() {
        $module = Yii::$app->getModule('reputation');
        
        $function = $module->settings->space()->get('functions', ReputationBase::DEFAULT_FUNCTION);
        $logarithmBase = $module->settings->space()->get('logarithm_base', ReputationBase::DEFAULT_LOGARITHM_BASE);
        $create_content = $module->settings->space()->get('create_content', ReputationBase::DEFAULT_CREATE_CONTENT);
        $smb_likes_content = $module->settings->space()->get('smb_likes_content', ReputationBase::DEFAULT_SMB_LIKES_CONTENT);
        $smb_favorites_content = $module->settings->space()->get('smb_favorites_content', ReputationBase::DEFAULT_SMB_FAVORITES_CONTENT);
        $smb_comments_content = $module->settings->space()->get('smb_comments_content', ReputationBase::DEFAULT_SMB_COMMENTS_CONTENT);
        $daily_limit = $module->settings->space()->get('daily_limit', ReputationBase::DEFAULT_DAILY_LIMIT);
        $decrease_weighting = $module->settings->space()->get('decrease_weighting', ReputationBase::DEFAULT_DECREASE_WEIGHTING);
        $lambda_short = $module->settings->space()->get('cron_job', ReputationBase::DEFAULT_CRON_JOB);
        $lambda_long = $module->settings->space()->get('lambda_short', ReputationBase::DEFAULT_LAMBDA_SHORT);
        $ranking_new_period = $module->settings->space()->get('lambda_long', ReputationBase::DEFAULT_LAMBDA_LONG);

        $spaceSettings = array($function, $logarithmBase, $create_content, $smb_likes_content,
            $smb_favorites_content, $smb_comments_content, $daily_limit, $decrease_weighting,
            $lambda_short, $lambda_long, $ranking_new_period);

        return $spaceSettings;
    }

    /**
     * Count all likes a content object has received. Do not count likes from user who created this post
     *
     * @param Content $content : The content object
     * @param $userId : The user id
     * @param $cacheId : The cache id
     * @param bool $forceUpdate : true if cache should be ignored
     * @return Like[]
     */
    protected function getLikesFromContent(Content $content, $userId, $cacheId, $forceUpdate = false) {
        $likes = Yii::$app->cache->get($cacheId);

        if ($likes === false || $forceUpdate === true) {
            $object = $content->object_model;
            $objectModel = $object::tableName();
            $likes = array();
            try {
                $query = Like::find();
                $query->leftJoin($objectModel . ' AS p', 'like.object_id = p.id');
                $query->leftJoin('content AS ct', 'p.id = ct.object_id');
                $condition = 'ct.id=:contentId AND like.created_by!=:userId AND ct.created_by=:userId AND like.object_model=:objectModel AND ct.object_model=:objectModel';
                $params = array(':contentId' => $content->id, ':objectModel' => $objectModel, ':userId' => $userId);
                $query->where($condition, $params);
                $query->all();

                Yii::$app->cache->set($cacheId, $query, ReputationBase::CACHE_TIME_SECONDS);
            } catch (Exception $e) {
                Yii::trace('Couldn\'t fetch likes from object model: ' . $objectModel);
            }
        }

        return $likes;
    }

    /**
     * Count all favorites a content object has received. Do not count favorites from user who created this post
     *
     * @param Content $content : The content object
     * @param $userId : The user id
     * @param $cacheId : The cache id
     * @param bool $forceUpdate : true if cache should be ignored
     * @return Favorite[]
     */
    protected function getFavoritesFromContent(Content $content, $userId, $cacheId, $forceUpdate = false) {
        $favorites = Yii::$app->cache->get($cacheId);

        if ($favorites === false || $forceUpdate === true) {
            $object = $content->object_model;
            $objectModel = $object::tableName();
            $favorites = array();

            // not possible to favorite comments atm
            if (strcmp($objectModel, 'comment') == 0) {
                return array();
            }

            try {
                $query = Favorite::find();
                $query->leftJoin($objectModel . ' AS p', 'favorite.object_id = p.id');
                $query->leftJoin('content AS ct', 'p.id = ct.object_id');

                $condition = 'ct.id=:contentId AND favorite.created_by!=:userId AND ct.created_by=:userId AND favorite.object_model=:objectModel AND ct.object_model=:objectModel';
                $params = array(':contentId' => $content->id, ':objectModel' => $objectModel, ':userId' => $userId);
                $query->where($condition, $params);
                $query->all();

                Yii::$app->cache->set($cacheId, $query, ReputationBase::CACHE_TIME_SECONDS);
            } catch (Exception $e) {
                Yii::trace('Couldn\'t fetch favorites from object model: ' . $objectModel);
            }
        }

        return $favorites;
    }

}
