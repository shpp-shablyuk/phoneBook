<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $fio
 * @property integer $city_id
 */
class Users extends \yii\db\ActiveRecord
{
	private $_country_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fio', 'city_id', 'country_id'], 'required'],
            [['city_id', 'country_id'], 'integer'],
            [['fio'], 'string', 'max' => 542],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'FIO',
            'city_id' => 'City',
            'country_id' => 'Country',
        ];
    }

	public function getCity()
	{
		return $this->hasOne(Cities::className(), ['id' => 'city_id']);
	}

	public function getCountry()
	{
		return $this->hasOne(Countries::className(), ['id' => 'country_id'])
			->viaTable('cities', ['id' => 'city_id']);
	}

	public function getPhones()
	{
		return $this->hasMany(Phones::className(), ['user_id' => 'id']);
	}

	public function getPhoneList()
	{
		return implode(', ', ArrayHelper::getColumn($this->phones, 'phone'));
	}

	public function setcountry_id($id)
	{
		$this->_country_id = (int) $id;
	}

	public function getcountry_id()
	{
		if (!$this->city_id) {
			return null;
		}

		if ($this->_country_id === null) {
			$this->setcountry_id($this->getCountry()->one()->id);
		}

		return $this->_country_id;
	}
}
