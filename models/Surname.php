<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "surname".
 *
 * @property int $id_surname
 * @property string $surname
 */
class Surname extends AbstractActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'surname';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surname'], 'required'],
            [['surname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_surname' => 'Id Surname',
            'surname' => 'Surname',
        ];
    }
}
