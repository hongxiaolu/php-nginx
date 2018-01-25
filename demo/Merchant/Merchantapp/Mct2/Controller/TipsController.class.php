<?php
namespace Mct2\Controller;
use Think\Controller;
class TipsController extends Controller {
    public function auth(){
		$this->display();
    }
    public function token(){
		session(null); 
		$this->display();
    }	
}