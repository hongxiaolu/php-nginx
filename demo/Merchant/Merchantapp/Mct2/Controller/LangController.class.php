<?php
namespace Mct2\Controller;
use Think\Controller;
class LangController extends Controller {
    public function index(){
		if(!empty($_SERVER['HTTP_REFERER'])){
        	redirect($_SERVER['HTTP_REFERER']);
		}else{
			redirect('/');
		}
    }

}