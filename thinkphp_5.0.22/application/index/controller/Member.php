<?php
/**
 * User: set (79839='czx')
 * createTime: 2019/1/3  21:16
 * description:
 */

namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\captcha\Captcha;
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
        $request = request();
        if($request->isPost()){
            $input_data = input('param.') ;
            //验证验证码
            //if(!$this->check_verify($input_data['captcha_code']))  $this->error("验证码错误");

            $user_info = Db::table("tp_member")->where("user_name ='{$input_data['user_name']}' and   password='{$input_data['password']}' ")->find();
            if($user_info){
                Session::set("user_info" , $user_info);
                //登录成功
                $this->success('登录成功', url('index'));
            }else{
                //登录失败
                $this->error('登录失败 , 账号或密码错误');
            }
        }else{
            return $this->fetch();
        }
    }
    
    /*
     *   注册
     *   邮箱验证码功能需要做校验功能(避免频繁刷发邮件验证码)
     *   我还是把注册地址放到邮箱里面， 进去做激活操作。  有效期为2天，
     */
    public function register()
    {
        $request = request();
        if($request->isPost()){
            $input_data = input('param.') ;
            halt($input_data);
        }
        return $this->fetch();
    }

    /*
     *    更改密码
     */
    public function new_password()
    {
        return $this->fetch();
    }



    /*
 *   验证码图片
 */
    public function captcha_image()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }

    function check_verify($code, $id = ''){
        $captcha = new Captcha();
        return $captcha->check($code, $id);
    }
}