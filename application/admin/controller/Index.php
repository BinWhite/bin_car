<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\admin\controller;
use think\View;
use think\Controller;
/**
 * Description of index
 *
 * @author Administrator
 */
class Index extends Controller{
    //put your code here
    public function index(){
        $loginToken = \think\Session::get("loginToken");
        if($loginToken){
            echo 1;
        }else{
            $url = \think\Url::build('login/index');
            $this->error("请先登录！",$url);
        }
    }
}
