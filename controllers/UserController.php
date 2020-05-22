<?php

namespace app\controllers;

use app\behaviors\NotificationsBehavior;
use app\interfaces\MultipleUsersNotification;
use app\models\NotificationSettings;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use function array_filter;
use function is_int;
use function is_integer;
use function print_r;
use function var_dump;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller implements MultipleUsersNotification
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'send-spam' => ['POST'],
                ],
            ],
            [
                'class' => NotificationsBehavior::class,
                'messages' => [
                    NotificationSettings::USER_SPAM => 'Hi all!',
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionSendSpam()
    {
        Yii::$app->session->setFlash('success', 'Spam has been sended.');
        $this->trigger(NotificationSettings::USER_SPAM);

        return $this->redirect(['user/index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function getUsersForNotify(): array
    {
        $ids = \Yii::$app->request->post('ids');
        foreach ($ids as $key => &$id) {
            $id = (int)$id;
            if (!$id) {
                unset($ids[$key]);
            }
        }

        return User::find()->select('id,email')->where(['id' => $ids])->all();
    }
}
