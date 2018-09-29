<?php
namespace app\index\controller;

use think\Controller;
use think\View;
use think\Db;
use think\Session;
use think\Loader;
use think\Log;

define('FRUIT_CATALOG_ID',1);
define('SEAFOOD_CATALOG_ID',2);
define('MEAT_CATALOG_ID',3);
define('VAGETABLE_CATALOG_ID',5);

class Home extends CommonController
{
    public function index(){
        $view=new View();
        $fruit_list = $this->get_goods_by_catalog(FRUIT_CATALOG_ID);
        $view->assign('fruit_list',$fruit_list);
        $seafood_list = $this->get_goods_by_catalog(SEAFOOD_CATALOG_ID);
        $view->assign('seafood_list',$seafood_list);
        Log::record($fruit_list);
        return $view->fetch('index');
    }
    public function get_goods_by_catalog($catalog_id){
        $good_id_list = Db::table('good_home')
                ->where('catalog_id',$catalog_id)
                ->column('good_id');
        $goods = [];
        if(!empty($good_id_list)){
            $goods = Db::table('good')
                    ->where(['good_id'=>['IN',$good_id_list]])
                    ->limit(4)
                    ->select();
        }
        foreach ($goods as $key => $good) {
            $goods[$key]['original_img_src'] = SITE_PUBLIC_URL.$good['original_img'];
            $goods[$key]['detail_url'] = url('index/home/detail',['good_id'=>$good['good_id'] ]);
        }
        Log::record($goods);
        return $goods;
    }
    public function detail($good_id){
        $catalog_list = Db::table('good_catalog')->select();
        
        $good = DB::table('good')
                ->where('good_id',$good_id)
                ->find();
        $good['catalog_name'] = get_good_catalog_name($catalog_list,$good['catalog_id']);
        $good['sale_state'] = get_sale_state($good['is_on_sale']);
        $good['original_img_src'] = SITE_PUBLIC_URL.$good['original_img'];

        $view=new View();
        $view->assign('good',$good);
        return $view->fetch('detail');
    }
    public function good_list($catalog_id,$keyword=''){
        $where_map = [];

        if($catalog_id != 0){
            $where_map['catalog_id'] = $catalog_id;

            $catalog = Db::table('good_catalog')
                    ->where('catalog_id',$catalog_id)
                    ->find();
            $catalog_name = $catalog['catalog_name'];
        }else{
            $catalog_name = "所有商品";
        }
        $this->assign('catalog_name', $catalog_name);

        if(!empty($keyword)){
            $where_map['good_name'] = ['LIKE',"%$keyword%"];
        }

        $list = Db::table('good')
            ->where($where_map)
            ->paginate(15,false,['query'=>request()->param() ]);


        $this->assign('list', $list);
        return $this->fetch();
    }
}
