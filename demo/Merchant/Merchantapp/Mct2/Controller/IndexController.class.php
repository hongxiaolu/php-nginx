<?php
namespace Mct2\Controller;
use Think\Controller;
class IndexController extends Controller {
	
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(107);
		$this->admin=$admin;
		$this->assign('menuid',1);
		$map['news_num']=8;
		$map['notice_num']=3;
		$map['app_num']=5;
		$cache=10;
		$cache_key=md5('/home/index/?scr_id='.$admin['scr_id'].'&lang='.strtolower(LANG_SET));
		$result=$Tlinx->get('',$map,$cache,$cache_key);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		#今天的交易数据处理
		foreach(array('today','yestoday','seven_days','thirty_days') as $days){ 
			$trade_array=null;
			if(!empty($result['data']['trade'][$days]['in'])){
				foreach($result['data']['trade'][$days]['in'] as $value){
					$trade_array[$value['currency']]=$value;
					$trade_array[$value['currency']]['avg_trade_amount']=$value['trade_amount']/$value['trans_num'];
				}
			}
			if(!empty($result['data']['trade'][$days]['out'])){
				foreach($result['data']['trade'][$days]['out'] as $value){
					if(!empty($trade_array[$value['currency']])){
						$trade_array[$value['currency']]['original_amount']  +=-$value['original_amount'];
						$trade_array[$value['currency']]['discount_amount']  +=-$value['discount_amount'];
						$trade_array[$value['currency']]['trade_amount']     +=-$value['trade_amount'];
						$trade_array[$value['currency']]['trans_num']    	 +=$value['trans_num'];				
					}else{
						$trade_array[$value['currency']]['original_amount']  =-$value['original_amount'];
						$trade_array[$value['currency']]['discount_amount']  =-$value['discount_amount'];
						$trade_array[$value['currency']]['trade_amount']     =-$value['trade_amount'];
						$trade_array[$value['currency']]['currency']         =$value['currency'];
						$trade_array[$value['currency']]['currency_sign']    =$value['currency_sign'];
						$trade_array[$value['currency']]['trans_num']    	 =$value['trans_num'];
					}
				}
			}
			if(empty($trade_array)){
				$trade_array[0]['original_amount']=0;
				$trade_array[0]['discount_amount']=0;
				$trade_array[0]['trade_amount']=0;
				$trade_array[0]['currency']='';
				$trade_array[0]['currency_sign']='';
				$trade_array[0]['trans_num']=0;
				$trade_array[0]['avg_trade_amount']=0;
			}
			$this->assign($days.'_trade',$trade_array);
		}

		$this->rows=$result['data'];
		$this->map=$map;
		$this->display();
    }
	
    public function help(){
		$admin=session('admin');
		if(!empty($admin)){
			import("Mct2.Util.Tlinx");
			$Tlinx=new \Tlinx();
			$this->admin=$Tlinx->check_auth(0);	
			$this->assign('menuid',0);		
		}
		
		$this->display();
    }
		
    public function alipay(){
		$admin=session('admin');
		import("Mct2.Util.Tlinx");
		if(!empty($admin)){
			$Tlinx=new \Tlinx();
			$this->admin=$Tlinx->check_auth(0);		
			$this->assign('menuid',0);	
		}else{
			$Tlinx=new \Tlinx(0);	
		}
		$result=$Tlinx->api('alipay');
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->url=$result['data']['url'];
		
		$this->display();
    }		
}