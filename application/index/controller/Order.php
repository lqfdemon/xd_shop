<?php
namespace app\index\controller;
use think\Session;
use think\View;
use think\Db;
use think\Controller;
use think\Log;

class Order extends CommonController
{  
    public function place_order($order_no){
        $order_info = Db::table('order')
                        ->where('order_no',$order_no)
                        ->find();
        if(empty($order_info)){
            $this->error('订单信息为空');
        }

        $customer_info = Db::table('customer')
                        ->where('id',$order_info['customer_id'])
                        ->find();
        if(empty($customer_info)){
            $this->error('顾客信息为空');
        }

        $full_address = $customer_info['address']."   (".$customer_info['contact_name']."   收)"."  联系电话：".$customer_info['contact_tel'];

        $good_list = Db::table('order_goods')
                    ->where('order_no',$order_no)
                    ->select();
        $total_good_cost = 0;
        foreach ($good_list as $key => $good) {
            $good_info = Db::table('good')
                            ->where('good_id',$good['good_id'])
                            ->find();
            $good_list[$key]['good_name'] = $good_info['good_name'];
            $good_list[$key]['measurement'] = $good_info['measurement'];
            $good_list[$key]['img_src'] =SITE_PUBLIC_URL.$good_info['original_img'];
            $good_list[$key]['good_cost'] = $good['good_price']*$good['good_num'];
            $total_good_cost += $good_list[$key]['good_cost'] ;
        }       

        $transit_cost =  10;//运费
        $total_cost = $total_good_cost + $transit_cost;

        $this->assign('order_no',$order_no);
        $this->assign('transit_cost',$transit_cost);
        $this->assign('total_good_cost',$total_good_cost);
        $this->assign('total_cost',$total_cost);
        $this->assign('full_address',$full_address);
        $this->assign('good_list',$good_list);
        return $this->fetch();
    }
    public function union_pay(){
        $order_no = "2018092956985557";
        $total_cost = 100.00;
        $this->assign('order_no',$order_no);
        $this->assign('total_cost',$total_cost);
        return $this->fetch();
    }
}

