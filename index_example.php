<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . './application/');
define('PUBLIC_PATH','../../public/');
define('SITE_PUBLIC_URL','http://localhost/xd_shop/');
define('LOCAL_FILE_PATH','/Uploads/');
define('GOOD_ORIGINAL_IMG_FILE_PATH',__DIR__.'/public/GoodOriginalIMG/');
define('GOOD_ORIGINAL_IMG_PATH','public/GoodOriginalIMG/');
// 加载框架引导文件
require __DIR__ . './thinkphp/start.php';
