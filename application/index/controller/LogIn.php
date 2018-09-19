<?php
namespace app\index\controller;
use think\Session;
use think\View;
use think\Db;
use think\Controller;
use think\Log;

use app\index\model\Customer;

class LogIn extends Controller
{
    public function log_in(){
        $view=new View();
        return $view->fetch('log_in');        
    }
    public function log_check($emp_no,$password){
        if (empty($emp_no)) {
            $this -> error('帐号必须！');
        } elseif (empty($password)) {
            $this -> error('密码必须！');
        }

        $user = Customer::where('emp_no',$emp_no)->find();
        if(empty($user)){
            $this->error("账号不存在");
        }

        if($user['password']==md5($password)){
            Session::set('customer_id', $user['id']);
            Session::set('customer_name', $user['name']);
            $this->success();
        }else{
            $this->error("密码错误");
        }      
    }    
    public function login_off(){
        Session::set('customer_id',false);
        $this->redirect(url('log_in'));
    }
}