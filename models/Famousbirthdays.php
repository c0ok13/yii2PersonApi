<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "famousbirthdays".
 *
 * @property int $id_person
 * @property int $full_name
 */
class Famousbirthdays extends AbstractActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'famousbirthdays';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name'], 'required'],
            [['full_name'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_person' => 'Id Person',
            'full_name' => 'Full Name',
        ];
    }
}
