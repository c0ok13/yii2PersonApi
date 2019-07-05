<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wiki".
 *
 * @property int $id_wiki
 * @property string $wiki_name
 */
class Wiki extends AbstractActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wiki';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wiki_name'], 'required'],
            [['wiki_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_wiki' => 'Id Wiki',
            'wiki_name' => 'Wiki Name',
        ];
    }
}
