<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banned_word".
 *
 * @property int $id_word
 * @property string $word
 */
class BannedWord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banned_word';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['word'], 'required'],
            [['word'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_word' => 'Id Word',
            'word' => 'Word',
        ];
    }
}
