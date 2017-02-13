<?php

/**
 * Description of humhub\modules\reputation\models\ReputationContent
 *
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation\models;

use Yii;
use \DateTime;

/**
 * This is the model class for table "reputation_content".
 *
 * The followings are the available columns in table 'reputation_content':
 * @property integer $id
 * @property integer $score
 * @property float $score_short
 * @property float $score_long
 * @property integer $content_id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @author Anton Kurnitzky
 */
class ReputationContent extends ReputationBase {

    const CACHE_TIME_SECONDS = 900; // Default caching time is 15minutes

    /**
     * @inheritdoc
     */

    public static function tableName() {
        return 'reputation_content';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('score, score_short, score_long, content_id', 'required'),
            array('score, content_id, created_by, updated_by', 'integerOnly' => true),
            array('created_at, updated_at', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'content' => array(self::BELONGS_TO, 'Content', 'content_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'score' => 'Score',
            'score_short' => 'Score short',
            'score_long' => 'Score long',
            'content_id' => 'Content',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        );
    }

    /**
     * Recalculate content reputation for a space
     *
     * @param $space : The space where the content should be updated
     * @param bool $forceUpdate : Ignore cache
     */
    public function updateContentReputation($space, $forceUpdate = false) {
        $spaceSettings = ReputationBase::getSpaceSettings($space);
        $spaceContent = ReputationBase::getContentFromSpace($space);        
        $lambda_short = $spaceSettings['lambda_short'];
        $lambda_long = $spaceSettings['lambda_long'];

        foreach ($spaceContent as $content) {
            $cacheId = 'reputation_space_content' . '_' . $space->id . '_' . $content->id;
            $contentReputation = Yii::$app->cache->get($cacheId);

            if ($contentReputation === false || $forceUpdate === true) {
                $contentReputation = [];
                // get all reputation_content objects from this space
                $params = array('content_id' => $content->id);
                $contentReputation = ReputationContent::findOne($params);
                if ($contentReputation == null) {
                    // Create new reputation_content entry
                    $contentReputation = new ReputationContent();
                    $contentReputation->content_id = $content->id;
                    $contentReputation->created_by = $content->created_by;
                }
                $score = ReputationContent::calculateContentReputationScore($content, $space, $forceUpdate);
                $contentReputation->score = $score;
                $timePassed = ReputationContent::getTimeInHoursSinceContentCreation($content->created_at);
                $contentReputation->score_long = ReputationContent::getDecayedScore($score, $timePassed, $lambda_long);
                $contentReputation->score_short = ReputationContent::getDecayedScore($score, $timePassed, $lambda_short);
                $contentReputation->updated_at = date('Y-m-d H:i:s');
                $contentReputation->updated_by = $content->updated_by;
                $contentReputation->save(false);

                Yii::$app->cache->set($cacheId, $contentReputation, ReputationBase::CACHE_TIME_SECONDS);
            }
        }
    }
    
    /*
     * Calculate the reputation score for all content objects inside this space
     * Use the count of likes, favorites and comments and the reputation settings to calculate this
     *
     * @param $content
     * @param $space
     */
    public function calculateContentReputationScore($content, $container, $forceUpdate) {
        $spaceSettings = ReputationBase::getSpaceSettings($container);
        $cacheId = 'likes_earned_cache_' . $content->id;
        $likes = ReputationBase::getLikesFromContent($content, $content->created_by, $cacheId, $forceUpdate);
        if ($container->isModuleEnabled('favorite')) {           
            // now count the favorites this content earned from other users
            $cacheId = 'favorites_earned_cache_' . $content->id;
            $favorites = ReputationBase::getFavoritesFromContent($content, $content->created_by, $cacheId, $forceUpdate);
        } else {
            $favorites = [];
        }
        $cacheId = 'comments_earned_cache_' . $content->id;
        $comments = ReputationBase::getCommentsFromContent($content, $content->created_by, $cacheId, true, $forceUpdate);
        return (count($likes) * $spaceSettings['smb_likes_content'] + count($favorites) * $spaceSettings['smb_favorites_content'] + count($comments) * $spaceSettings['smb_comments_content']);
    }

    /**
     * Calculate time in hours since the content was created
     *
     * @param $createdAt : The creation time of the content object
     * @return int: Time in hours since content was created
     */
    private function getTimeInHoursSinceContentCreation($createdAt) {
        $now = new DateTime();
        $createdTime = new DateTime($createdAt);
        $timeSinceCreation = round(($now->getTimestamp() - $createdTime->getTimestamp()) / 3600, 2, PHP_ROUND_HALF_UP);

        // do not allow zero because this value is used as divisor later
        if ($timeSinceCreation == 0) {
            $timeSinceCreation = 1;
        }

        return $timeSinceCreation;
    }

    /**
     *    Return the value of a decayed score, that is,
     *    a value that decreases over time.
     *    The formula used for the decay is exp(-lambda * t^2),
     *    where lambda is damping factor and t is the age
     *    of the object in seconds.
     *    If lambda is 0, no decay takes place, whatsoever.
     *
     * @param    int /float    Initial score.
     * @param    int    Time in hours that has passed since the score was set.
     * @param    float    Damping factor.
     * @return    float    Decayed score.
     */
    public function getDecayedScore($score, $age, $lambda = 0) {
        // Actual calculation: exp(-lambda * t^2)
        return ($score + 1) * exp(-$lambda * $age * $age);
    }

}
