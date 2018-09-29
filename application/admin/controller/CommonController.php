<?php
namespace app\admin\controller;
use think\Session;
use think\View;
use think\Db;
use think\Controller;
use think\Log;
use app\index\model\File;

class CommonController extends Controller
{  
    
    public function _initialize() {
		if(!Session::has('admin_id')){
            $this->redirect('admin/log_in/log_in');
            return;
        }
    }
    
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        Log::record($file);
        $max_size = 200*1024*1024;
        $ext_allow = 'ppt,pptx,xls,xlsx,jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,pdf,ceb';
        // 移动到框架应用根目录/public/uploads/ 目录下
        $cur_date = date("Y-m");
        Log::record($cur_date);
        $dir = FILE_DOWNLOAD_ROOT_PATH.'file/'.$cur_date;
        $info = $file->validate(['size'=>$max_size,'ext'=>$ext_allow])
                     ->rule('uniqid')
                     ->move($dir);
        Log::record($info);
        Log::record($dir);
        if($info){
            $savepath='file/'.$cur_date.'/';
            $file_name = $info->getFilename();
            $data=[ 'name'=>$_POST['name'],
                    'savename'=>$file_name,
                    'savepath'=>$savepath,
                    'ext'=>$info->getExtension(),
                    'md5'=>$info->hash('md5'),
                    'sha1'=>$info->hash('sha1'),
                    'size'=>$_POST['size'],
                    'create_time'=>time(),
            ];
            $file_modal = new File();
            $file_modal->save($data);
            Log::record('ext='.$data['ext']);
            if($data['ext'] == 'doc'||$data['ext'] == 'docx'){
                $this->create_preview($data['savename'],$data['savepath']);
            }
            return $file_modal->id;
        }else{
            // 上传失败获取错误信息
            Log::record($info->getError());
        }
    }
    public function download($file_id){     
        $File = new File();
        $root = FILE_DOWNLOAD_ROOT_PATH;   
        if (false === $File -> download($root, $file_id)) {
            $this -> error($File -> getError());
        }   
    }
    //数字金额转大写金额
    public  function NumToCNMoney($num){
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2); 
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
                return "金额太大，请检查";
        } 
        $i = 0;
        $c = "";
        while (1) {
                if ($i == 0) {
                        //获取最后一位数字
                        $n = substr($num, strlen($num)-1, 1);
                } else {
                        $n = $num % 10;
                }
                //每次将最后一位数字转化为中文
                $p1 = substr($c1, 3 * $n, 3);
                $p2 = substr($c2, 3 * $i, 3);
                if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                        $c = $p1 . $p2 . $c;
                } else {
                        $c = $p1 . $c;
                }
                $i = $i + 1;
                //去掉数字最后一位了
                $num = $num / 10;
                $num = sprintf("%d", $num+0.01);
                //结束循环
                if ($num == 0) {
                        break;
                } 
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
                //utf8一个汉字相当3个字符
                $m = substr($c, $j, 6);
                //处理数字中很多0的情况,每次循环去掉一个汉字“零”
                if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                        $left = substr($c, 0, $j);
                        $right = substr($c, $j + 3);
                        $c = $left . $right;
                        $j = $j-3;
                        $slen = $slen-3;
                } 
                $j = $j + 3;
        } 
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c)-3, 3) == '零') {
                $c = substr($c, 0, strlen($c)-3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
                return "零元整";
        }else{
                return $c . "整";
        }
    }
     //发送POST 请求
    public function request_post($url = '', $param ) {
        if (empty($url) || empty($param)) {
            return false;
        } 
        $post_fields = "";
        foreach ($param as $key => $value) {
            $post_fields = $post_fields."$key=".urlencode($value)."&";
        }
        Log::record($post_fields);
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);        
        return $data;

    }
    public function http_request($url, $data = null)
    {
         $curl = curl_init();
         curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
         if (!empty($data)){
             curl_setopt($curl, CURLOPT_POST, 1);
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         }
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
         $output = curl_exec($curl);
         curl_close($curl);
         return $output;
     }
}
