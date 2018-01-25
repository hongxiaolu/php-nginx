<?php
namespace Mct2\Controller;
use Think\Controller;
class GetwayController extends Controller {
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(102); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('post.');
		
		#先查询session有没有应用令牌
		$app_token=session('app_token');
		if(empty($app_token[$map['app_id']])){
			$this->error('应用令牌过期，无法访问');
			exit();
		}
		$rows=$app_token[$map['app_id']];
	
		#解密数据
		$data_str=$Tlinx->aes_decode($map['app_data'],$rows['aes_key']);
		
		$array=json_decode($data_str,true);
		if(!is_array($array)){
			$str='<h1>AES DECRYPT ERROR</h1>';
			$str.='<hr>MAP:'.http_build_query($map);
			$str.='<hr>DECRYPT:'.$data_str;
			$str.='<hr>JSON:'.json_encode($array);
			if(APP_DEBUG){
				echo $str;
				die();
			}else{
				$this->error('应用提交数据有误，请联系应用客服人员。<br/>解密app_data数据失败');
				exit();	
			}
		}
		if($map['trade_type']=='gotopay'){
			if(empty($array['app_id'])){$this->error('app_id is null');exit();}
			if(empty($array['trade_no'])){$this->error('trade_no is null');exit();}							
			if(empty($array['ord_name'])){$this->error('ord_name is null');exit();}
			if(!isset($array['original_amount'])){$this->error('original_amount is null');exit();}			
			if(!isset($array['discount_amount'])){$this->error('discount_amount is null');exit();}	
			if(!isset($array['no_discount_amount'])){$this->error('no_discount_amount is null');exit();}	
			if(!isset($array['trade_amount'])){$this->error('trade_amount is null');exit();}	
			if(!isset($array['print'])){$this->error('print is null');exit();}									
			if(!isset($array['pay_shop'])){$this->error('pay_shop is null');exit();}				
			if(!isset($array['card_bag'])){$this->error('card_bag is null');exit();}
			session('gotopay',$array);
			echo '
			<script type="text/javascript">
			top.location.href="/pos";
			</script>';	
		}else{
			$this->error('未知交易类型');	
		}
    }
}