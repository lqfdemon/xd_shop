<?php
namespace app\index\controller;
use think\Session;
use think\View;
use think\Db;
use think\Controller;
use think\Log;

class Cart extends CommonController
{  
    public function my_cart(){
        $this->assign('not_show_cart',true);
        return $this->fetch();
    }
    public function get_good_num_in_cart(){
        $customer_id = session('customer_id');
        return Db::table('cart')
                ->where('customer_id',$customer_id)
                ->count('id');
    }
    public function add_good_to_cart(){
        $customer_id = session('customer_id');
        $cart_data=$_POST;
        $cart_data['customer_id'] = $customer_id;
        
        $where_map['customer_id'] = $customer_id;
        $where_map['good_id'] = $cart_data['good_id'];
        $old_cart_data = Db::table('cart')
                            ->where($where_map)
                            ->find();
        if(!empty($old_cart_data)){
            $good_num = $old_cart_data['good_num'] + $cart_data['good_num'];
            DB::table('cart')
                ->where($where_map)
                ->update(['good_num'=>$good_num]);
        }else{
            Db::table('cart')->insert($cart_data);
        }
        return $this->get_good_num_in_cart();
    }
    public function get_goods_in_cart(){
        $where_map['customer_id'] = session('customer_id');
        
        $goods = Db::table('cart')
                    ->where($where_map)
                    ->select();
        foreach ($goods as $key => $good) {
            $good_detail = Db::table('good')
                    ->where('good_id',$good['good_id'])
                    ->find();
            if(empty($good_detail)){
                $goods[$key]['price'] =  0;
                $goods[$key]['measurement'] = "-";
            }else{
                $goods[$key]['price'] = sprintf("%.2f",$good_detail['price']);
                $goods[$key]['measurement'] =  $good_detail['measurement'];
            }
            $goods[$key]['img_src'] = SITE_PUBLIC_URL.$good_detail['original_img'];
        } 
        return $goods;
    }
    public function create_order(){
        $carts_str = $_POST['carts_data'];
        $cart_list = explode(';',$carts_str);
        $order_no = build_order_no();
        $order_data = [
            'order_no'=>$order_no,
            'customer_id'=>session('customer_id'),
            'create_time'=>date("Y-m-d H:i:s"),
            'state'=>0,   //刚生成的订单 未支付状态
        ];
        Log::record($order_data);
        Db::table('order')->insert($order_data);

        foreach ($cart_list as $key => $cart) {
            $cart_data = explode('-',$cart);
            $cart_id = $cart_data[0];
            $good_num = $cart_data[1];
            $cart_info = Db::table('cart')
                            ->where('id',$cart_id)
                            ->find();
            if(empty($cart_info)){
                $this->error('未找到购物车信息');
            }    

            $good_info = Db::table('good')
                            ->where('good_id',$cart_info['good_id'])
                            ->find();
            if(empty($good_info)){
                $this->error('未找到商品信息');
            }  

            $order_goods_data=[
                'order_no'=>$order_no,
                'good_id'=>$cart_info['good_id'],
                'good_num'=>$good_num,
                'good_price'=>$good_info['price'],
            ];
            Db::table('cart')->where('id',$cart_id)->delete();

            Db::table('order_goods')->insert($order_goods_data);
        }
        $this->success("操作成功",'',$order_no);
    }
}


function build_order_no(){
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}
