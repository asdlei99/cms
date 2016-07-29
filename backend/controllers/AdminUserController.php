<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31
 * Time: 17:01
 */
namespace backend\controllers;

use backend\models\AdminRoles;
use yii;
use backend\models\User;
use yii\data\ActiveDataProvider;
use backend\models\AdminRoleUser;

class AdminUserController extends BaseController
{

    public function actionIndex()
    {
        $query = User::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionCreate()
    {
        $model = new User();
        $model->setScenario('create');
        $rolesModel = new AdminRoleUser();
        if(yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) && $model->validate() && $rolesModel->load(yii::$app->request->post()) && $rolesModel->validate() && $model->save() ){
                $rolesModel->uid = $model->primaryKey;
                $rolesModel->save();
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
            }else{
                Yii::$app->getSession()->setFlash('error', yii::t('app', 'Error'));
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
        }
        $temp = AdminRoles::find()->asArray()->all();
        $roles = [];
        foreach ($temp as $v){
            $roles[$v['id']] = $v['role_name'];
        }
        return $this->render('create', [
            'model' => $model,
            'rolesModel' => $rolesModel,
            'roles' => $roles
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->getModel($id);
        $model->setScenario('update');
        $rolesModel = AdminRoleUser::findOne(['uid'=>$id]);
        if($rolesModel == NULL){
            $rolesModel = new AdminRoleUser();
            $rolesModel->uid = $id;
        }
        if ( Yii::$app->request->isPost ) {
            if( $model->load(Yii::$app->request->post()) && $model->validate() && $rolesModel->load(yii::$app->request->post()) && $rolesModel->validate() && $model->save() && $rolesModel->save() ){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['update', 'id'=>$model->primaryKey]);
            }else{
                Yii::$app->getSession()->setFlash('error', yii::t('app', 'Error'));
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
            $model = User::findOne(['id'=>yii::$app->user->identity->id]);
        }

        $temp = AdminRoles::find()->asArray()->all();
        $roles = [];
        foreach ($temp as $v){
            $roles[$v['id']] = $v['role_name'];
        }
        return $this->render('update', [
            'model' => $model,
            'rolesModel' => $rolesModel,
            'roles' => $roles
        ]);
    }

    public function actionDelete($id)
    {
        if(yii::$app->request->getIsAjax()){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
        if($id == 1) throw new yii\web\ForbiddenHttpException(yii::t('app', "Not allowed to delete default super administrator admin"));
        return parent::actionDelete($id);
    }

    public function getModel($id = '')
    {
        return User::findOne(['id'=>$id]);
    }

    public function actionUpdateSelf()
    {
        $model = User::findOne(['id'=>yii::$app->user->identity->id]);
        $model->setScenario('self-update');
        if(yii::$app->request->isPost){
            if( $model->validate() && $model->load(yii::$app->request->post()) && $model->self_update() ){
                Yii::$app->getSession()->setFlash('success', '成功');
            }else{
                Yii::$app->getSession()->setFlash('error', '失败');
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
            $model = User::findOne(['id'=>yii::$app->user->identity->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateSelfAvatar()
    {
        $model = User::findOne(['id'=>yii::$app->user->identity->id]);
        $model->setScenario('update');
        if(yii::$app->request->isPost && $model->validate() && $model->load(yii::$app->request->post()) && $model->save()){
            return $this->redirect(['site/main']);
        }
        return $this->render('update-self-avatar', [
            'model' => $model,
        ]);
    }

    public function actionAssign($uid='')
    {
        $model = AdminRoleUser::findOne(['uid'=>$uid]);//->createCommand()->getRawSql();var_dump($model);die;
        if($model == ''){//echo 11;die;
            $model = new AdminRoleUser();
        }
        $model->uid = $uid;
        if( yii::$app->request->isPost ){
            if($model->load(yii::$app->request->post()) && $model->save()){
                Yii::$app->getSession()->setFlash('success', '成功');
            }else{//var_dump($model->getErrors());die;
                Yii::$app->getSession()->setFlash('error', '失败');
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
        }
        $temp = AdminRoles::find()->asArray()->all();
        $roles = [];
        foreach ($temp as $v){
            $roles[$v['id']] = $v['role_name'];
        }
        return $this->render('assign', [
            'model' => $model,
            'roles' => $roles,
        ]);
    }

    public function actionA()
    {echo 2;die;
        return $this->render('a');
    }
}