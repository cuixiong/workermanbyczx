<?php
/**
 * User: set (79839='czx')
 * createTime: 2019/1/3  21:16
 * description:
 */

namespace app\index\controller;
use think\Controller;

class Member extends Controller 
{   
    /*
     *   首页
     */
    public function index()
    {
        return $this->fetch();
    }
    
    /*
     *  登陆
     */
    public function login()
    {
        return $this->fetch();
    }
    
    /*
     *   注册
     */
    public function register()
    {
        return $this->fetch();
    }

    /*
     *    更改密码
     */
    public function new_password()
    {
        return $this->fetch();
    }
}