<?php

namespace app\models;

use Yii;

abstract class AbstractActiveRecord extends \yii\db\ActiveRecord
{

    /**
     * Returns the list of all attribute names of the model.
     * The default implementation will return all column names of the table associated with this AR class.
     * @return array list of attribute names.
     */
    public static function getAllAttributes()
    {
        return array_keys(static::getTableSchema()->columns);
    }

}
