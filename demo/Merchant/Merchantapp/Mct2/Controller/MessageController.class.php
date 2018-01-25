<?php
namespace Mct2\Controller;
use Think\Controller;
class MessageController extends Controller {
	#消息列表
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); //可以访问此页面的权限
		//取得所有参数
		session('heartbeat',null);
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		#机器令牌
		$map['tml_token']=session('tml_token');
		//调接口取数据
		$result=$Tlinx->get('message',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
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
		
		$this->map=$map;
		$this->assign('menuid',4);	
		$this->assign('left_menuid',1);		
		$this->display();
    }

	#用户列表
    public function online_user(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('message/online_user',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
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
			$this->assign('empty','<div class="no_data"><img src="/Public/mct2/images/2.0/no_data.png"><p>'.L('_NO_DATA_').'</p></div>');
			$this->page=null;
			$this->list=null;
		}
		
		$this->map=$map;
		$this->assign('menuid',4);	
		$this->assign('left_menuid',2);
		$this->display();
    }	
	
	//发送消息
    public function send(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); 
		$map=I('');
		$this->assign('map',$map);
		$this->assign('menuid',4);	
		$this->assign('left_menuid',1);	
		$this->display();
    }
	
	//发送消息
    public function send_to(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); 
		$map=I('');
		if($map['msg_type']==2){
			if($_FILES["file"]["error"] > 0){
  				$this->error($_FILES["file"]["error"]);
			}else{
				if($_FILES['file']['size']>2097152){
					$this->error(L('_MSG_ERR_FILE_SIZE_',array('file_size'=>number_format(($_FILES['file']['size']/1024/1024),2))));
					exit();					
				}
				if($_FILES['file']['type']!='image/jpeg' && $_FILES['file']['type']!='image/png' && $_FILES['file']['type']!='image/gif' && $_FILES['file']['type']!='image/jpg' && $_FILES['file']['type']!='audio/mp3'){
					$this->error(L('_MSG_ERR_FILE_TYPE_',array('file_type'=>$_FILES['file']['type'])));
					exit();	
				}
				#将图片读为二进制，再转16进制
				$handle = fopen($_FILES['file']['tmp_name'], "r");
				$data['file_content'] = fread($handle, filesize($_FILES['file']['tmp_name']));
				fclose($handle);
	
				$str_info  = @unpack("C2chars", $data['file_content']);
				$type_code = intval($str_info['chars1'].$str_info['chars2']);
				$file_type = '';
				switch ($type_code) {
					case 255216:
						$file_type = 'jpg';
						break;
					case 7173:
						$file_type = 'gif';
						break;
					case 13780:
						$file_type = 'png';
						break;
					case 7368:
						$file_type = 'mp3';
						break;					
					default:
						$file_type = 'unknown';
						break;
				}
				if($file_type=='unknown'){
					$this->error(L('_MSG_ERR_FILE_TYPE_',array('file_type'=>$_FILES['file']['type'])));
					exit();	
				}

				#提交图片到数据tlinx
				$data['file_content'] = base64_encode($data['file_content']);
				$data['file_ext'] = $file_type;
				$Tlinx->open_url=C('mct_upload_url');
				$result=$Tlinx->get('mct1/files',$data);
				if($result['errcode']==0){
					$map['content']=$result['data']['file_url'];
				}else{
					$this->error($result['msg']);	
					exit();
				}
				$Tlinx->open_url=C('mct_open_url');
				
			}
  		}
		$result=$Tlinx->get('message/send',$map);
		if($result['errcode']==0){
			$this->success($result['msg']);	
		}else{
			$this->error($result['msg']);
			exit();
		}
	}
	
	//删除
    public function delete(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); 
		$map=I('');
		$result=$Tlinx->get('message/delete',$map);
		if($result['errcode']==0){
			$this->success($result['msg']);	
		}else{
			$this->error($result['msg']);
		}
    }
	
	//设置ok
    public function setok(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); 
		$map=I('');
		$result=$Tlinx->get('message/setok',$map);
		if($result['errcode']==0){
			$this->success($result['msg']);	
		}else{
			$this->error($result['msg']);
		}
    }
	
	#修改头像
    public function face(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); //可以访问此页面的权限
		$this->assign('menuid',4);	
		$this->assign('left_menuid',3);
		$this->display();
	}	
	
	/*修改头像保存*/
	public function face_save(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); //可以访问此页面的权限

		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  =     C('TEMP_PATH'); // 设置附件上传根目录
		$upload->autoSub = false;
		
		// 上传文件 
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$this->error($upload->getError());
			exit();
		}else{// 上传成功
			//上传的文件路径
			$filepath=$upload->rootPath.$info['photo']['savepath'].$info['photo']['savename'];
			
			$image = new \Think\Image();//创建对象
			$image->open($filepath);//加载图片
			$width=100;
			$height=100;
			//如果上传的文件尺寸与要裁剪的尺寸不一致，则执行裁剪
			if($image->width()!=$width && $image->height()!=$height){
				$image->thumb($width, $height,\Think\Image::IMAGE_THUMB_CENTER)->save($filepath,null,90);
			}
			
			#将图片读为二进制，再转16进制
			$handle = fopen($filepath, "r");
			$contents = base64_encode((fread($handle, filesize ($filepath))));
			fclose($handle);
			//删除原图
			unlink($filepath);
			#提交数据
			
			#提交图片到数据tlinx
			$data['file_content']=$contents;
			$data['file_ext']=$info['photo']['ext'];
			$Tlinx->open_url=C('mct_upload_url');
			$result=$Tlinx->get('mct1/face',$data);
			if($result['errcode']==0){
				$Tlinx->open_url=C('mct_open_url');
				$user=$Tlinx->get('user');
				session('admin',$user['data']);
				redirect('/message/face');
			}else{
				$this->error($result['msg']);
			}
		}

	}

	/*心跳接口*/
	public function heartbeat(){
		$heartbeat=session('heartbeat');
		if(empty($heartbeat) || time()-$heartbeat['time']>10){
			import("Mct2.Util.Tlinx");
			$Tlinx=new \Tlinx();
			$this->admin=$Tlinx->check_auth(114); //可以访问此页面的权限
			$result=$Tlinx->get('message/heartbeat');
			$heartbeat['count']=$result['data']['count'];
			$heartbeat['time']=time();
			session('heartbeat',$heartbeat);
		}
		ob_clean();
		echo ($heartbeat['count']);	
	}
}
