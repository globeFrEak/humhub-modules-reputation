<?php

/*
 * The space configuration form
 *
 * @author Anton Kurnitzky (v0.11) & Philipp Horna (v0.20+) */

namespace humhub\modules\reputation\models;

use Yii;

class SpaceSettings extends \yii\base\Model
{
    public $functions;
    public $logarithmBase;
    public $create_content;
    public $smb_likes_content;
    public $smb_favorites_content;
    public $smb_comments_content;
    public $daily_limit;
    public $decrease_weighting;
    public $cron_job;
    // advanced settings
    public $lambda_long; // ranking hot
    public $lambda_short; // ranking rising

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('functions', 'required'),
            array('logarithmBase', 'required'),
            array('logarithmBase', 'number', 'min' => 1),
            array('create_content', 'required'),
            array('create_content', 'number', 'min' => 0),
            array('smb_likes_content', 'required'),
            array('smb_likes_content', 'number', 'min' => 0),
            array('smb_favorites_content', 'required'),
            array('smb_favorites_content', 'number', 'min' => 0),
            array('smb_comments_content', 'required'),
            array('smb_comments_content', 'number', 'min' => 0),
            array('daily_limit', 'required'),
            array('smb_comments_content', 'number', 'min' => 0),
            array('decrease_weighting', 'required'),
            array('cron_job', 'required'),
            array('lambda_long', 'required'),
            array('lambda_long', 'double'),
            array('lambda_short', 'required'),
            array('lambda_short', 'double'),
        );
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'functions' => Yii::t('ReputationModule.forms_adminController_settings', 'Function'),
            'logarithmBase' => Yii::t('ReputationModule.forms_adminController_settings', 'Logarithm base'),
            'create_content' => Yii::t('ReputationModule.forms_adminController_settings', 'Creating posts or comments'),
            'smb_likes_content' => Yii::t('ReputationModule.forms_adminController_settings', 'Somebody liked the post'),
            'smb_favorites_content' => Yii::t('ReputationModule.forms_adminController_settings', 'Somebody marked the post as favorite'),
            'smb_comments_content' => Yii::t('ReputationModule.forms_adminController_settings', 'Somebody comments the post'),
            'daily_limit' => Yii::t('ReputationModule.forms_adminController_settings', 'Daily limit for Users'),
            'decrease_weighting' => Yii::t('ReputationModule.forms_adminController_settings', 'Decrease weighting per post'),
            'cron_job' => Yii::t('ReputationModule.forms_adminController_settings', 'Update reputation data on hourly cron job'),
            'lambda_long' => Yii::t('ReputationModule.forms_adminController_settings', 'Exponential decrease for Ranking Hot'),
            'lambda_short' => Yii::t('ReputationModule.forms_adminController_settings', 'Exponential decrease for Ranking Rising'),
        );
    }
}