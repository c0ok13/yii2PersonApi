<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "name".
 *
 * @property int $id_name
 * @property string $name
 */
class Name extends AbstractActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'name';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_name' => 'Id Name',
            'name' => 'Name',
        ];
    }
}
