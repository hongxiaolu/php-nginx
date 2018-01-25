<?php
namespace Mct2\Controller;
use Think\Controller;
class PosappController extends Controller {
	#收银台应用列表
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(102); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=15;
		$map['app_type']='1';
		//调接口取数据
		$result=$Tlinx->get('shopapp',$map);
		if($result['data']['pages']['totalnum']>0){
			$Page  = new \Think\Page($result['data']['pages']['totalnum'],$map['pagesize']);
			$Page->setConfig('header',L('_PAGE_HEADER_'));
			$Page->setConfig('theme', '<div class="fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </div><span>%HEADER%</span>');
			foreach($map as $key=>$val) {
				$Page->parameter[$key]   =   urlencode($val);
			}
			// 赋值分页输出
			$this->page=$Page->show();
			$this->list=$result['data']['list'];
		}else{
			$this->page=null;
			$this->list=null;
		}
		if(!isset($map['keyword'])){
			$map['keyword']=null;
		}
		if(empty($map['app_id'])){
			$map['app_id']="";
		}
		if(empty($map['size'])){
			$map['size']="";
		}
		if(empty($map['type'])){
			$map['type']="";
		}				
		$this->map=$map;
		$this->assign('menuid',8);
		$this->assign('left_menuid',2);	
		$this->display();
    }
	
	#进入应用(html5)主页
    public function entry(){
		C('SHOW_PAGE_TRACE',false);//不显示跟踪调试
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(102); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		$this->map=$map;
		$this->display();
	}

	#进入应用(html5)头部
	public function top(){
		C('SHOW_PAGE_TRACE',false);//不显示跟踪调试
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(102); //可以访问此页面的权限
		$this->assign('menuid',8);	
		$this->display();
	}
		
	#进入应用(html5)主页
    public function main(){
		C('SHOW_PAGE_TRACE',false);//不显示跟踪调试
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(102); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		#先查询session有没有应用令牌
		$app_token=session('app_token');
		if(empty($app_token[$map['app_id']])){
			$app_token=self::app_token($Tlinx,$admin,$map);
		}
		$rows=$app_token[$map['app_id']];
		

		if(empty($rows['tml_url']) || $rows['tml_url']=="" || strlen($rows['tml_url'])<10){
			$this->error('当前应用暂时无法进入','',1000);
			exit();
		}
		#加密数据
		$data['mct_no']=$admin['mct_no'];
		$data['shop_no']=$admin['shop_no'];
		if(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=='on'){
			$data['getway_url']='https://'.$_SERVER['HTTP_HOST'].'/getway';
		}else{
			$data['getway_url']='http://'.$_SERVER['HTTP_HOST'].'/getway';	
		}
		$data['timestamp']=time();
		if(!empty($map['return_str'])){
			$data['return_str']=$map['return_str'];
		}
		$data_str=$Tlinx->aes_encode(json_encode($data),$rows['aes_key']);
		#进入机器访问端
		$this->app_token=$rows['token'];
		$this->data=$data_str;
		$this->rows=$rows;
		$this->display();
	}
	
	#交换应用令牌
	public function app_token($Tlinx,$admin,$map){
		$app_token=session('app_token');
		#获取应用访问令牌
		$result=$Tlinx->get('shopapp/entry',$map);
		if($result['errcode']>0){
			$this->error($result['msg']);
			exit();	
		}else{
			$app_token[$map['app_id']]=$result['data'];
		}
		$url=$app_token[$map['app_id']]['token_url'];
		$get['token']=$app_token[$map['app_id']]['token'];
		$get['trade_type']='app_token';
		$data['mct_no']=$admin['mct_no'];
		$data['shop_no']=$admin['shop_no'];
		$data['timestamp']=time();
		$post['data']=$Tlinx->aes_encode(json_encode($data),$app_token[$map['app_id']]['aes_key']);
		
		$result=$Tlinx->http_query($url,$get,$post);
		if($result){
			$arr_result=json_decode($result,true);	
			if(is_array($arr_result)){
				if($arr_result['errcode']>0){
					$this->error($arr_result['msg']);	
					exit();						
				}else{
					$arr_result['data']=json_decode($Tlinx->aes_decode($arr_result['data'],$app_token[$map['app_id']]['aes_key']),true);
					if(!empty($arr_result['data']['app_token'])){
						$app_token[$map['app_id']]['token']=$arr_result['data']['app_token'];
						session('app_token',$app_token);
						return $app_token;
					}else{
						$str='APP_URL:'.$url;
						$str.='<hr>GET:'.http_build_query($get);
						$str.='<hr>POST:'.http_build_query($post);
						$str.='<hr>RESPONSE:'.$result;	
						$str.='<hr>解密数据失败';						
						if(APP_DEBUG){
							echo $str;
							die();
						}else{
							$this->error('获取令牌失败');	
							exit();	
						}	
					}
				}
			}else{
				$str='APP_URL:'.$url;
				$str.='<hr>GET:'.http_build_query($get);
				$str.='<hr>POST:'.http_build_query($post);
				$str.='<hr>RESPONSE:'.$result;
				$str.='<hr>返回非JSON数据';	
				if(APP_DEBUG){
					echo $str;
					die();
				}else{
					$this->error('访问应用失败,请稍后重试');	
					exit();	
				}
			}
		}else{
			$str='APP_URL:'.$url;
			$str.='<hr>GET:'.http_build_query($get);
			$str.='<hr>POST:'.http_build_query($post);
			$str.='<hr>RESPONSE:'.$result;
			$str.='<hr>获取数据为空';	
			if(APP_DEBUG){
				echo $str;
				die();
			}else{
				$this->error('访问应用失败,请稍后重试');	
				exit();	
			}
		}
	}

	#更新应用版本
    public function updatever(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(102); //可以访问此页面的权限
		$map=I('');
		$map['update_ver']=1;
		$result=$Tlinx->get('shopapp/appver',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/posapp',10000);
		}else{
			$this->error($result['msg']);
		}
	}
	
	#卡券包首页
    public function cardbag_index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(98); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		if(empty($map['ord_no'])){
			$this->error('单号不能为空');
			exit();	
		}
		$payapp=session('payapp');
		$this->payapp=$payapp;
		if(empty($map['app_id'])){
			$map['app_id']=$payapp[0]['app_id'];
		}
		foreach($payapp as $value){
			if($value['app_id']==$map['app_id']){
				$this->cur_app=$value;
				break;
			}	
		}
		if(empty($map['trade_type'])){
			if(!empty($this->cur_app['setting']['query'])){
				$map['trade_type']='query';
			}else{
				$map['trade_type']='trade';	
			}
		}
		if($map['trade_type']=='trade'){
			$this->cur_fields=$this->cur_app['setting']['trade']['fields'];
		}else{
			$this->cur_fields=$this->cur_app['setting']['query']['fields'];	
		}
		$this->map=$map;
		$this->cardbag_reault=S('cardbag_reault_'.$admin['scr_id']);
		$this->display();
	}	
	
	#卡券包查询
    public function cardbag_query(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(98); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		\Think\Log::record('MAP:'.http_build_query($map),'INFO');
		$post_map=$map;
		$post_map['mct_no']=$admin['mct_no'];
		$post_map['shop_no']=$admin['shop_no'];
		$post_map['timestamp']=time();
		$post_map['language']='zh-cn';

		#先查询session有没有应用令牌
		$app_token=session('app_token');
		if(empty($app_token[$post_map['app_id']])){
			$app_token=self::app_token($Tlinx,$admin,$post_map['app_id']);
		}

		if($post_map['trade_type']=='query'){
			$url=$app_token[$post_map['app_id']]['setting']['query']['url'];
		}else if($post_map['trade_type']=='trade'){
			$url=$app_token[$post_map['app_id']]['setting']['trade']['url'];
		}else if($post_map['trade_type']=='refund'){
			$url=$app_token[$post_map['app_id']]['setting']['refund']['url'];
		}
		$get['app_token']=$app_token[$post_map['app_id']]['token'];
		if(APP_DEBUG){
			\Think\Log::record('GET:'.$url,'INFO');
			\Think\Log::record('POST:'.json_encode($post_map),'INFO');
			\Think\Log::record('GET:'.json_encode($get),'INFO');
		}
		$post['data']=$Tlinx->aes_encode(json_encode($post_map),$app_token[$post_map['app_id']]['aes_key']);
		$result=$Tlinx->http_query($url,$get,$post);
		if(APP_DEBUG){
			\Think\Log::record('RESULT:'.$result,'INFO');
		}
		$result=json_decode($result,true);
		if($result['errcode']>0){
			$this->error($result['msg']);	
		}else{
			if(isset($result['data'])){
				$result['data']=$Tlinx->aes_decode($result['data'],$app_token[$post_map['app_id']]['aes_key']);
				if(APP_DEBUG){
					\Think\Log::record('STR_DATA:'.$result['data'],'INFO');
				}
				$result['data']=json_decode($result['data'],true);
				if(APP_DEBUG){
					\Think\Log::record('JSON_DECODE:'.json_encode($result),'INFO');
				}
			}
			#成功后的跳转意图
			if($map['trade_type']=='query'){
				session('cardbag_data',$result['data']);#查询数据存session
				$map['trade_type']='trade';
				if(count($result['data'])>1){
					$map['trade_type']='select';
				}else{
					if(isset($result['data'][0]['rows_id'])){
						$map['rows_id']=$result['data'][0]['rows_id'];
					}
				}
			}else if($map['trade_type']=='trade' && isset($result['data']['trade_no'])){
				session('cardbag_data',null);#清空查询数据session
				#将新的优惠券存进数组
				$cardbag_reault=S('cardbag_reault_'.$admin['scr_id']);
				$cardbag_reault[$map['ord_no']][$map['app_id']][$result['data']['trade_no']]=$result['data'];
				S('cardbag_reault_'.$admin['scr_id'],$cardbag_reault);
				#重返查询页面
				$new_map['app_id']=$map['app_id'];
				$new_map['ord_no']=$map['ord_no'];
				$new_map['trade_type']='query';
				$map=null;
				$map=$new_map;
			}else if($map['trade_type']=='refund'){
				#将新的优惠券存进数组
				$cardbag_reault=S('cardbag_reault_'.$admin['scr_id']);
				
				\Think\Log::record('cardbag_reault:'.json_encode($cardbag_reault),'INFO');
				
				\Think\Log::record('cardbag_reault_trade:'.json_encode($cardbag_reault[$map['ord_no']][$map['trade_no']]),'INFO');
				
				unset($cardbag_reault[$map['ord_no']][$map['app_id']][$map['trade_no']]);	
				if(count($cardbag_reault[$map['ord_no']][$map['app_id']])==0){
					unset($cardbag_reault[$map['ord_no']][$map['app_id']]);	
				}
				if(count($cardbag_reault[$map['ord_no']])==0){
					unset($cardbag_reault[$map['ord_no']]);	
				}
				\Think\Log::record('cardbag_reault:'.json_encode($cardbag_reault),'INFO');
				
				S('cardbag_reault_'.$admin['scr_id'],$cardbag_reault);
				$new_map['app_id']=$map['app_id'];
				$new_map['ord_no']=$map['ord_no'];
				$new_map['trade_type']='query';
				$map=null;
				$map=$new_map;
			}
			
			if(APP_DEBUG){
				\Think\Log::record('REDIRECT:'.http_build_query($map),'INFO');
			}
			$this->success($result['msg'],'/posapp/cardbag_index?'.http_build_query($map));	
		}
		

	}	
	
	#卡券包交易结果
	public function cardbag_result(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(98); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		$cardbag_reault=S('cardbag_reault_'.$admin['scr_id']);
		if($cardbag_reault){
			if(isset($cardbag_reault[$map['ord_no']])){
				$str='<table>';
				$textarea='';
				$discount=0;
				$payapp=session('payapp');
				foreach($cardbag_reault[$map['ord_no']] as $app_id=>$app_value){ //循环应用编号
					$app_name='';
					foreach($payapp as $payvalue){ //循环本地缓存数据获得应用名称
						if($app_id==$payvalue['app_id']){
							$app_name=$payvalue['app_name'];
						}
					}
					foreach($app_value as $trade_no=>$app){
						$str.='<tr>';
						$str.='<th>'.$app_name.'</th>';
						$str.='<td>交易号：'.$app['trade_no'].'</td>';	
						$str.='<td>交易金额：￥'.($app['amount']/100).'</td>';	
						if($app['type']==2){
							$str.='<td>折扣金额：￥'.($app['discount']/100).'</td>';
							$discount+=$app['discount'];
						}elseif($app['type']==5){
							$str.='<td>抵现金额：￥'.($app['discount']/100).'</td>';
							$discount+=$app['discount'];
						}elseif($app['type']==3){
							$str.='<td>积分：'.$app['discount'].'</td>';
						}elseif($app['type']==4){
							$str.='<td>礼品：'.$app['remark'].'</td>';
						}	
						if($app['type']==4){
							$str.='<td></td>';
						}else{
							$str.='<td>备注：'.$app['remark'].'</td>';	
						}
						$str.='</tr>';
						$textarea.='<textarea style="display:none" name="app_para[]">'.json_encode($app).'</textarea>';
					}

				}
				$str.='<dl>';
				$data['discount']	=$discount;
				$data['str']		=$str;
				$data['textarea']	=$textarea;
				$this->ajaxReturn(array('errcode'=>0,'data'=>$data));	
			}else{
				$this->ajaxReturn(array('errcode'=>$map['ord_no']));		
			}
		}else{
			$this->ajaxReturn(array('errcode'=>$map['ord_no']));	
		}
	}
	
	#订单应用交易结果通知回调
	public function app_notice(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(98); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		$app_notice=S('app_notice_'.$admin['scr_id']);#通知缓存数据
		if(!is_array($app_notice) || count($app_notice)==0){
			$this->ajaxReturn($map);
			exit();
		}
		$app_token=session('app_token');#获取应用缓存信息
		foreach($app_notice as $app_key=>$app_value){
			$app_id=$app_value['app_id'];
			#获取app_token
			if(empty($app_token[$app_id])){
				$app_token=self::app_token($Tlinx,$admin,$app_id);
			}
			$url=$app_token[$app_id]['token_url'];
			$get['app_token']=$app_token[$app_id]['token'];
			$get['trade_type']='order';
			$data['trade_no']=$app_value['trade_no'];
			$data['status']=$app_value['status'];
			$data['ord_no']=$app_value['ord_no'];
			$data['mct_no']=$admin['mct_no'];
			$data['shop_no']=$admin['shop_no'];
			$data['timestamp']=time();
			if(APP_DEBUG){
				\Think\Log::record('APP_NOTICE_DATA:'.json_encode($data),'INFO');
			}
			$post['data']=$Tlinx->aes_encode(json_encode($data),$app_token[$app_id]['aes_key']);
			$result=$Tlinx->http_query($url,$get,$post);
			if(APP_DEBUG){
				\Think\Log::record('APP_HTTP_RESULT:'.$result,'INFO');
			}
			if($result){
				$arr_result=json_decode($result,true);
				if(is_array($arr_result)){
					if($arr_result['errcode']>0){
						unset($app_notice[$app_key]);
					}else{
						$arr_result['data']=$Tlinx->aes_decode($arr_result['data'],$app_token[$app_id]['aes_key']);
						\Think\Log::record('APP_DECODE:'.$arr_result['data'],'INFO');
						$arr_result['data']=json_decode($arr_result['data'],true);
						if(APP_DEBUG){
							\Think\Log::record('APP_TO_ARRAY:'.json_encode($arr_result),'INFO');
						}
						if(!empty($arr_result['data']['trade_no'])){
							$app_notice=S('app_notice_'.$admin['scr_id']);
							unset($app_notice[$app_key]);
							if($map['ord_no']==$app_value['ord_no']){
								$map2['app_id']=$app_id;
								if(!empty($arr_result['data']['return_str'])){
									$map2['return_str']=$arr_result['data']['return_str'];
								}else{
									$map2['return_str']=null;
								}
							}
						}else{
							if(APP_DEBUG){
								$map['msg']='解密数据失败';
							}else{
								$map['msg']='通知失败';	
							}	
						}
					}
				}else{
					$map['msg']='notice fail'.$result;	
				}				
			}else{
				$map['msg']='notice fail'.$result;	
			}
		}
		if(count($app_notice)==0){
			S('app_notice_'.$admin['scr_id'],null);
		}else{
			S('app_notice_'.$admin['scr_id'],$app_notice);
		}
		if(!empty($map2)){
			$this->ajaxReturn($map2);
		}else{
			$this->ajaxReturn($map);
		}
		
	}
}