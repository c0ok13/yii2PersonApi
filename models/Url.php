<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "url".
 *
 * @property int $id_url
 * @property string $url
 */
class Url extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'url';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url'], 'required'],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_url' => 'Id Url',
            'url' => 'Url',
        ];
    }
}
