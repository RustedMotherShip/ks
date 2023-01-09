<?php
namespace app\controllers;
use app\models\User;
use app\models\LoginForm;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;

class UserController extends FunctionController
{
    public $modelClass = 'app\models\User';
    public function behaviors()
    {
        /*
         * Указание на аутентификации по токену
         */
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only'=>['logout,del'] //Перечислите для контроллера методы, требующие авторизации
        ];
        return $behaviors;
    }

public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->send(200, ['content'=>['code'=>200, 'message'=>'Success']]);
    }
    
public function actionCreate(){
        $request=Yii::$app->request->post(); //получение данных из post запроса
        $user=new User($request); // Создание модели на основе присланных данных
        if (!$user->validate()) return $this->validation($user); //Валидация модели
        $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password); //хэширование пароля
        $user->save(false);//Сохранение модели в БД
}
public function actionLogin(){
        $request=Yii::$app->request->post();//Здесь не объект, а ассоциативный массив
        $loginForm=new LoginForm($request);
        if (!$loginForm->validate()) return $this->validation($loginForm);
        $user=User::find()->where(['inv_num'=>$request['inv_num']])->one();
        if (isset($user) && Yii::$app->getSecurity()->validatePassword($request['password'], $user->password)){
            $user->token=Yii::$app->getSecurity()->generateRandomString();
            $user->save(false);
            return $this->send(200, ['content'=>['token'=>$user->token]]);
        }
        return $this->send(401, ['content'=>['code'=>401, 'message'=>'Bad']]);
    }
      public function actionDel(){
        $user=Yii::$app->user->identity;
        $user->delete();
        return $this->send(200, ['message'=>'Пользователь удален!']);
    }
    public function actionFetchalluser()
    {
    if (!$this->is_admin()) return $this->send(401, ['bad']);
        $res = User::find()->IndexBy('id')->all();
        return $this->send(200, ['All users:' => $res]);
    }
    public function actionRecreate($id)
    {
        $request=Yii::$app->request->getBodyParams();
        $user=User::findOne($id);
        if (!$user) return $this->send(404,  ['content'=>['code'=>404, 'Bad']]);
        $user=Yii::$app->user->identity;
        if (isset($request['inv_num'])) $user->inv_num = $request['inv_num'];
        if (isset($request['password'])) $user->password = $request['password']  = Yii::$app->getSecurity()->generatePasswordHash($user->password);
        if (isset($request['is_admin'])) $user->is_admin = $request['is_admin'];

        if (!$user->validate()) return $this->validation($user);
        $user->save();
            return $this->send(200, ['code'=>200, 'Updated']);
}
  public function actionAccount()
    {
    $user=Yii::$app->user->identity; // Получить идентифицированного пользователя
    return $this->send(200,['You'=>$user]);
    }
}