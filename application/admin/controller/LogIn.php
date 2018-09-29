<?php
namespace app\admin\controller;

use think\Session;
use think\View;
use think\Db;
use think\Controller;
use think\Log;

use app\index\model\User;

class LogIn extends Controller
{
    public function log_in(){
        $view=new View();
        return $view->fetch();        
    }
    public function log_check(){
        $emp_no = $_POST['emp_no'];
        $psw=$_POST['psw'];
        if (empty($emp_no)) {
            $this -> error('帐号必须！');
        } elseif (empty($psw)) {
            $this -> error('密码必须！');
        }
        $user = User::where('emp_no',$emp_no)->find();
        if(empty($user)){
            $this->error("账号不存在");
        }
        if($user['password']==md5($psw)){
            Session::set('admin_id', $user['id']);
            Session::set('admin_name', $user['name']);
            $this->success();
        }else{
            $this->error("密码错误");
        }      
    }    
    public function logOff(){
        Session::set('admin_id',false);
        $this->redirect(url('log_in'));
    }
}