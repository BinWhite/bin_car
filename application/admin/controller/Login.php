<?php
namespace app\admin\controller;

use think\View;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author Administrator
 */
class Login{
    //put your code here
    public function index(){
        return view("login/login");
    }
    
    public function login(){
        $db = \think\Db::connect();
        echo json_encode(array("data"=>"","code"=>400,"msg"=>"1111"));
    }
}
