<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coupon".
 *
 * @property int $id_coupon
 * @property string $image_url
 * @property string $title
 * @property string $text
 * @property string $ending_date
 */
class Coupon extends AbstractActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coupon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image_url', 'title'], 'required'],
            [['ending_date'], 'safe'],
            [['image_url', 'title', 'text'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_coupon' => 'Id Coupon',
            'image_url' => 'Image Url',
            'title' => 'Title',
            'text' => 'Text',
            'ending_date' => 'Ending Date',
        ];
    }
}
