<?php
namespace app\index\controller;
use think\Session;
use think\View;
use think\Db;
use think\Controller;
use think\Log;

use app\index\model\User;

class LogIn extends Controller
{
    public function test(){
        $view=new View();
        return $view->fetch('test');        
    }
    public function log_in(){
        $view=new View();
        return $view->fetch('log_in');        
    }

    public function login_check(){
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
            Session::set('amin_id', $user['id']);
            Session::set('amin_name', $user['name']);
            $this->success();
        }else{
            $this->error("密码错误");
        }      
    }    
    public function login_off(){
        Session::set('id',false);
        $this->redirect(url('log_in'));
    }
}