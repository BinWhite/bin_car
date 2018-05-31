<?php

namespace app\admin\controller;

use think\View;
use app\admin\model\member;
use app\admin\model\loginLog;

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
class Login extends \think\Controller {

    private $result = array(
        "code" => 400,
        "data" => array(),
        "msg" => "服务繁忙，请稍后重试！"
    );
    //前置操作函数数组
    protected $beforeActionList = [
        '__before' => ['only' => 'index']
    ];

    /**
     * 首页
     * @return type
     */
    public function index() {
        return view("login/login");
    }

    /**
     * 登录
     */
    public function login() {
        $member = new member();
        $map = array();
        $name = trim($_POST['username']);
        $info = $member->get(['name'=>$name]);
        if (empty($info)) {
            $this->result['msg'] = "账号不存在！";
        } else {
            $info = $info->getData();
            $password = md5(trim($_POST['password']));
            if ($password === $info['password']) {
                $loginToken = sha1($info['name'] . "|" . $info['id'] . "|" . time());
                cookie("loginToken",$loginToken,3600);
//                \think\Cookie::set("loginToken", $loginToken, 3600);
                $data = array();
                $data['uid'] = $info['id'];
                $data['loginToken'] = $loginToken;
                $data['type'] = 1;
                $loginResult = loginLog::create($data);
                if (!empty($loginResult)) {
                    $this->result['code'] = 1;
                    $this->result['msg'] = "登录成功！";
                }
            } else {
                $this->result['msg'] = "密码错误！";
            }
        }
        $this->__output();
    }
    
    public function logout(){
        cookie("loginToken",null);
        $url = \think\Url::build('login/index');
        $this->success("退出成功!",$url);
    }

    /**
     * 前置操作
     */
    protected function __before() {
        $loginToken = cookie("loginToken");
//        $loginToken = \think\Cookie::get("loginToken");
        if (!empty($loginToken)) {
            $url = \think\Url::build('index/index');
            $this->redirect($url);
        }
    }

    /**
     * 输出方法
     */
    protected function __output() {
        echo json_encode($this->result);
    }

}
