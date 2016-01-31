<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "images".
 *
 * @property integer $id
 * @property string $filename
 */
class Images extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'images';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename'], 'required'],
            [['filename'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'FileName',
        ];
    }
    
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            // ...custom code here...
            unlink(Yii::getAlias('@common').'/images/'.$this->filename);
            unlink(Yii::getAlias('@common').'/images/thumbnail/'.$this->filename);
            return true;
        } else {
            return false;
        }
    }    
}
