<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $inv_num
 * @property string $password
 * @property int $is_admin
 * @property string $token
 *
 * @property Cargo[] $cargos
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inv_num', 'password', 'is_admin', 'token'], 'required'],
            [['inv_num', 'is_admin'], 'integer'],
            [['password', 'token'], 'string', 'max' => 255],
            [['inv_num'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inv_num' => 'Inv Num',
            'password' => 'Password',
            'is_admin' => 'Is Admin',
            'token' => 'Token',
        ];
    }

    /**
     * Gets query for [[Cargos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCargos()
    {
        return $this->hasMany(Cargo::class, ['id' => 'id']);
    }
 public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['id' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    public static function findByLogin($inv_num)
    {
        return static::findOne(['inv_num' => $inv_num]);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return ;
    }

    public function validateAuthKey($authKey)
    {
        return ;
    }
    public function validatePassword($password)

    {
        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        if (Yii::$app->getSecurity()->validatePassword($password, $hash)) {
            return $this;
        } else {
            return 0;
        }
    }

    public function fields()
    {
        $fields = parent::fields();
// удаляем небезопасные поля
        unset($fields['id'],$fields['password']);
        return $fields;
    }
}
