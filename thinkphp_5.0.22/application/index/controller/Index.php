<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Config;
use think\captcha\Captcha;

class Index  extends Controller
{
    /*
     *   聊天页面
     */
    public function index()
    {
        $session = Session::get("user_info");
        $session = $session ? $session[0] : NULl;
        if($session){
            //$webServerIpAddress = config('webServerIpAddress');
            //return dump($webServerIpAddress);
            $this->assign("user_info" , $session);
            //$this->assign("webServerIpAddress" , $webServerIpAddress);
            return $this->fetch();
        }else{
            $this->error("你还没有登陆" , url('user_login'));
        }
    }

    /*
     *   用户登录
     */
    public function user_login()
    {
        $request = request();
        if($request->isPost()){
           $input_data = input('param.') ;
           //验证验证码
           if(!$this->check_verify($input_data['captcha_code']))  $this->error("验证码错误");

           $user_info = Db::query("select * from tp_member where user_name='{$input_data['user_name']}' and  password='{$input_data['password']}' ");

           if($user_info){
               Session::set("user_info" , $user_info);
               //登录成功
               $this->success('登录成功', url('index'));
           }else{
               //登录失败
               $this->error('登录失败');
           }
        }else{
          return $this->fetch();
        }
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


    //------------------------- 下面是测试


    public function hello()
    {
//        $ip_addr = config('workermanRegisterAddress');
//        return $ip_addr;
        Config::set("default_return_type" , "json");
        return ["status"=>"200","msg"=>"成功"];

    }

    public function info($id)
    {
        dump(config());
        //http://tp5.phpfreemarker.com/index/Index/info/id/6
        return $id;
    }


    public function sayhello()
    {
        $list = Db::query('select * from tp_member');
        //Db::execute("update think_user set name='thinkphp' where status=1");
        // 链式操作
        /**
            Db::table('think_user')
            ->order('create_time')
            ->limit(10)
            ->where('status',1)
            ->select();
         */
    //dump($list);
        $this->assign('user_list', $list);
        //不带任何参数：  当前模块/默认视图目录/当前控制器（小写）/当前操作（小写）.html
        return $this->fetch();
    }

    /**
     * return :
     * author : czx
     * date:2018/12/26
     */
    public function del()
    {
        //    id/1 方式 不可以通过 get 获取到 id 的方法   但是可以通过 param()方式获取
        //获取请求参数
        $request = request();
        echo '请求参数：';
        dump($request->param());
    }
}
