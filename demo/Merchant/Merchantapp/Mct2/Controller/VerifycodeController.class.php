<?php
namespace Mct2\Controller;
use Think\Controller;
class VerifycodeController extends Controller {
	public function index(){
		ob_clean();
		$config =    array(
			'expire'	=>180,		//验证码的有效期（秒）
			'useCurve'	=>false,	//是否使用混淆曲线 默认为true
			'fontSize'	=>20,		// 验证码字体大小
			'length'	=>4,		// 验证码位数
			'useNoise'	=>false,	// 关闭验证码杂点
			'bg'		=>array(52,73,94)
		);
		$Verify =     new \Think\Verify($config);
		$Verify->entry();
		exit();
	}
	
}