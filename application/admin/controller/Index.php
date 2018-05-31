<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\admin\controller;

use think\View;
use think\Controller;
use think\Db;
use app\admin\model\loginLog;
use app\admin\model\member;

/**
 * Description of index
 *
 * @author Administrator
 */
class Index extends Controller {

    //前置操作函数数组
    protected $beforeActionList = [
        '__before'
    ];
    //管理员信息
    private $member = array();
    private $time;

    /**
     * 首页
     * @return type
     */
    public function index() {
        $this->getLeft();
        return view();
    }

    /**
     * 欢迎页
     */
    public function welcome() {
        $this->assign("nowTime", date('Y-m-d H:i:s', $this->time));
        return view();
    }

    /**
     * 获取侧边导航栏
     */
    private function getLeft() {
        $navList = array();

        $roleRuleField = array();
        $roleRuleField[] = "rule_id";

        $roleRuleMap = array();
        $roleRuleMap['role_id'] = $this->member['rid'];
        $roleRuleListResult = Db::name('role_rule')->field($roleRuleField)->where($roleRuleMap)->select();
        $ruleList = array();
        if (empty($roleRuleListResult)) {
            $url = \think\Url::build('login/logout');
            $this->errir("发生异常，请重新登录！", $url);
        } else {
            foreach ($roleRuleListResult as $k => $v) {
                $ruleList[] = $v['rule_id'];
            }
        }

        $ruleField = array();
        $ruleField[] = "id";
        $ruleField[] = "name";
        $ruleField[] = "parent";
        $ruleField[] = "controller";
        $ruleField[] = "action";
        $ruleMap = array();
        $ruleMap['rid'] = array("in", $ruleList);
        $order = array();
        $order['parent'] = "ASC";
        $ruleList = Db::name('rule')->field($ruleField)->order($order)->select();
        if (empty($ruleList)) {
            $url = \think\Url::build('login/logout');
            $this->errir("发生异常，请重新登录！", $url);
        } else {
            foreach ($ruleList as $k => $v) {
                if ($v['parent'] == 0) {
                    $url = \think\Url::build("{$v['controller']}/{$v['action']}");
                    $navList[$v['id']] = array(
                        "name" => $v['name'],
                        "url" => $url
                    );
                } else {
                    $url = \think\Url::build("{$v['controller']}/{$v['action']}");
                    $navList[$v['parent']]['child'][] = array(
                        "name" => $v['name'],
                        "url" => $url
                    );
                }
            }
            $this->assign("leftNav", $navList);
        }
    }

    /**
     * 前置操作
     */
    protected function __before() {
        $loginToken = cookie("loginToken");
        if (!empty($loginToken)) {
            $uid = loginLog::get(['loginToken' => $loginToken]);
            if (empty($uid)) {
                $url = \think\Url::build('login/index');
                $this->error("请先登录！", $url);
            } else {
                $uid = $uid->getData("uid");
                $info = member::get(['id' => $uid]);
                if (empty($info)) {
                    $url = \think\Url::build('login/index');
                    $this->error("登录异常，请重新登录！", $url);
                } else {
                    $info = $info->getData();
                    $this->member['uid'] = $info['id'];
                    $this->member['name'] = $info['name'];
                    $this->member['rid'] = $info['rid'];
                    $this->time = time();
                    $this->assign("member", $this->member);
                }
            }
        } else {
            $url = \think\Url::build('login/index');
            $this->error("请先登录！", $url);
        }
    }

}
