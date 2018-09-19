<?php
namespace app\index\controller;

use think\Controller;
use think\View;
use think\Db;
use think\Session;
use think\Loader;
use think\Log;

class Home extends CommonController
{
    public function index()
    {
        $view=new View();
        return $view->fetch('index');
    }
}
