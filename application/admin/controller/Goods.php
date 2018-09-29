<?php
namespace app\admin\controller;

use think\Controller;
use think\View;
use think\Db;
use think\Session;
use think\Loader;
use think\Log;



class Goods extends CommonController
{
    public function catalog()
    {
        return $this->fetch();
    }
    public function add(){
        $this->redirect('detail',['good_id'=>0]);
    }
    public function edit($good_id){
        $this->redirect('detail',['good_id'=>$good_id]);
    }
    public function detail($good_id){
        $this->assign('good_id',$good_id);
        $serv_path = $_SERVER['HTTP_HOST'].'/xd_shop';
        $this->assign('serv_path',$serv_path);

        $catalog_list = Db::table('good_catalog')->select();
        $this->assign('catalog_list',$catalog_list);

        return $this->fetch();
    }
    public function get_good_detail($good_id){
        $good = Db::table('good')->where('good_id',$good_id)->find();
        return $good;
    }
    public function save_good_detail(){
        $data =$_POST;
		unset($data['good_id']);
		$good_id = $_POST['good_id'];
		if($good_id==0){
			$good_id = Db::table('good')->insertGetId($data);
		}else{
			Db::table('good')->where('good_id',$good_id)->update($data);
		}
        $this->success($good_id);
    }
    public function get_goods_list(){ 

        $catalog_list = Db::table('good_catalog')->select();

        $where_map = [];
        if(!empty($_GET['good_name'])){
            $key_word = $_GET['good_name'];
            $where_map['good_name'] = ['LIKE',"%$key_word%"];
        }
        if(!empty($_GET['is_on_sale'])){
            $where_map['is_on_sale'] = $_GET['is_on_sale'];
        }

        $good_list=Db::table('good')
            ->where($where_map)
            ->select();
        for ($i=0; $i <count($good_list) ; $i++) { 
            $good_list[$i]['catalog_name'] = get_good_catalog_name($catalog_list,$good_list[$i]['catalog_id']);
            $good_list[$i]['sale_state'] = get_sale_state($good_list[$i]['is_on_sale']);
        }
        return $good_list;
    }
    public function upload_original_img(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        Log::record($file);
        $max_size = 200*1024*1024;
        $ext_allow = 'jpg,gif,png,jpeg,bmp';
        // 移动到框架应用根目录/public/uploads/ 目录下
        $cur_date = date("Y-m");
        Log::record($cur_date);
        $dir = GOOD_ORIGINAL_IMG_FILE_PATH.$cur_date;
        $info = $file->validate(['size'=>$max_size,'ext'=>$ext_allow])
                     ->rule('uniqid')
                     ->move($dir);
        Log::record($info);
        Log::record($dir);
        if($info){
            $file_name = $info->getFilename();
            $img_save_path = GOOD_ORIGINAL_IMG_PATH.$cur_date.'/'.$file_name;
            Log::record($img_save_path);
            return $img_save_path;
        }else{
            // 上传失败获取错误信息
            Log::record($info->getError());
        }
    }
}