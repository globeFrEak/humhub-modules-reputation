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
use humhub\modules\content\models\ContentContainerSetting;
use humhub\modules\space\models\Setting;

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

    //const DEFAULT_RANKING_NEW_PERIOD = '36';

    /**
     * Returns all content objects (posts, polls, etc.) from this space
     *
     * @param $space : The space Object
     * @param bool $forceUpdate : Ignore cache
     * @return Content[]
     */
    public function getContentFromSpace($container, $forceUpdate = false) {
        $cacheId = 'posts_created_cache' . '_' . $container->wall_id;
        $spaceContent = Yii::$app->cache->get($cacheId);
        if ($spaceContent === false || $forceUpdate === true) {

            $condition = 'contentcontainer_id=:spaceId AND object_model!=:activity';
            $params = [':spaceId' => $container->wall_id, ':activity' => 'humhub\modules\activity\models\Activity'];
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
     * Return an array with all space settings and call setSpaceSettings when not found
     * @param $container Object 
     * @return $spaceSettings array
     */
    protected function getSpaceSettings($container) {
        $getSettings = ContentContainerSetting::findAll(['module_id' => 'reputation', 'contentcontainer_id' => $container->wall_id]);

        if (count($getSettings) > 0) {
            foreach ($getSettings as $setting) {
                $spaceSettings[$setting['name']] = $setting['value'];
            }
        } else {
            $spaceSettings = self::setSpaceSettings($container);
        }
        return $spaceSettings;
    }

    /**
     * set standard Reputation settings onSpace
     * @param $container Object 
     * @return $spaceSettings array
     */
    public function setSpaceSettings($container) {
        $spaceSettings = [
            'functions' => self::DEFAULT_FUNCTION,
            'logarithm_base' => self::DEFAULT_LOGARITHM_BASE,
            'create_content' => self::DEFAULT_CREATE_CONTENT,
            'smb_likes_content' => self::DEFAULT_SMB_LIKES_CONTENT,
            'smb_likes_content' => self::DEFAULT_SMB_FAVORITES_CONTENT,
            'smb_favorites_content' => self::DEFAULT_SMB_COMMENTS_CONTENT,
            'smb_comments_content' => self::DEFAULT_SMB_COMMENTS_CONTENT,
            'daily_limit' => self::DEFAULT_DAILY_LIMIT,
            'decrease_weighting' => self::DEFAULT_DECREASE_WEIGHTING,
            'cron_job' => self::DEFAULT_CRON_JOB,
            'lambda_long' => self::DEFAULT_LAMBDA_SHORT,
            'lambda_short' => self::DEFAULT_LAMBDA_LONG];

        foreach ($spaceSettings as $name => $value) {
            Setting::Set($container, $name, $value, 'reputation');
        }
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
