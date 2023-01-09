<?php

namespace app\controllers;

use app\models\User;
use app\models\LoginForm;
use Yii;
use app\controllers\FunctionController;
use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;

use app\models\Cargo;
use yii\rest\ActiveController;
class CargoController extends FunctionController 
{
    public $modelClass = 'app\models\Cargo';

    public function behaviors()
    {
        /*
         * Указание на аутентификации по токену
         */
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['add', 'del', 'red','fetchall'], //  Перечислите для контроллера методы, требующие аутентификации

        ];
        return $behaviors;
    }


public function actionFetchall()
    {
    if (!$this->is_admin()) return $this->send(401, ['bad']);
        $res = Cargo::find()->IndexBy('id')->all();
        return $this->send(200, ['All Resourses:' => $res]);
    }
public function actionFetchone($id)
    {
    if (!$this->is_admin()) return $this->send(401, ['bad']);
        $resone = Cargo::find()->where(['user_id' => $id])->IndexBy('id')->all();
        return $this->send(200, ['This user stash:' => $resone]);     
    }
public function actionFetch()
{
        $user=Yii::$app->user->identity;
        $id = $user->id;
        $resone = Cargo::find()->where(['user_id' => $id])->IndexBy('id')->all();
        return $this->send(200, ['User stash:' => $resone]);
}
    
 public function actionAdd()
    {  
        $request=Yii::$app->request->getBodyParams();
        $user=Yii::$app->user->identity;
        $id = $user->id;
        $request['user_id'] = $id;
        $res = new Cargo($request); 
        if (!$res->validate()) return $this->validation($res); //Валидация модели
        $res->save();//Сохранение модели в БД
        return $this->send(200, ['content' => ['code' => 200, 'message' => 'Success']]);
    }
public function actionOneadd($id)
    {  
        $request=Yii::$app->request->getBodyParams();
        $user=Yii::$app->user->identity;
        $request['user_id'] = $id;
        $res = new Cargo($request); 
        if (!$res->validate()) return $this->validation($res); //Валидация модели
        $res->save();//Сохранение модели в БД
        return $this->send(200, ['content' => ['code' => 200, 'message' => 'Success']]);
    }
  public function actionDel($id)
  {
        $user=Yii::$app->user->identity;
        $res = Cargo::findOne($id);
        if (!$res) return $this->send(404,['messege'=>'Bad']);
        $gg = $user->id;
        $gg1 = $res->user_id;
        if ($gg!=$gg1) return $this->send(404,['messege'=>'Not your`s']);
        if (!$res->validate()) return $this->validation($res); //Валидация модели
        $res->delete();//Сохранение модели в БД
        return $this->send(201, [['code'=>200, 'message'=>'Success']]);
  }
  public function actionOnedel($id)
  {
        if (!$this->is_admin()) return $this->send(401, ['bad']);
        $user=Yii::$app->user->identity;
        $res = Cargo::findOne($id);
        if (!$res) return $this->send(404,['messege'=>'Bad']);  
        if (!$res->validate()) return $this->validation($res); //Валидация модели
        $res->delete();//Сохранение модели в БД
        return $this->send(201, [['code'=>200, 'message'=>'Success']]);
  }
  }