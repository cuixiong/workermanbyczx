<?php
namespace app\index\controller;
use think\cache\driver\Redis;
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
        if($session){
            //查出redis消息
            $redis_server = new Redis(config('redis_conf'));
            $message_list = $redis_server->get("message_list");
            if($message_list){
                $message_list = json_decode($message_list , true);
                $message_list = $this->message_sort($message_list , $session);
                $this->assign("message_list" , $message_list);
            }
            $this->assign("message_list" , $message_list);
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
     *   消息排序
     *    当user_id是自己的时候 class为show
     *    反之就是send
     */
    private function message_sort($message , $user_info){
        //halt($message);
        $handler_message = array();

        foreach ($message as &$value){
           if($value['user_id'] == $user_info['user_id']){
               $value['html_type'] = 'show';
           }else{
               $value['html_type'] = 'send';
           }

           switch ($value['type']){
               case 'image': $value['hand_content_type'] = $this->img_format($value['content']); break;
               case 'say'  : $value['hand_content_type'] = $this->say_format($value['content']); break;
               default: $value['hand_content_type'] = "有错误消息类型";
           }

           if($value['type'] == 'image') $value['hand_content_type'] = $this->img_format($value['content']);
           $handler_message[] = '<div class="'.$value['html_type'].'"><div class="msg"><img class="headSrc" src="'.$value['head_image'].'"><div class="p"><i class="msg_input"></i>'.$value['hand_content_type'].'</div></div></div>';
        }
        return $handler_message;
    }

    /*
     *   普通文字格式化
     */
    private function say_format($data){
        return "<p>{$data}<br></p>";
    }

    /*
     *   图片格式化
     */
    private function img_format($data){
        $image_id =  md5(mt_rand(10000000,99999999));
        return '<img id="'.$image_id.'" onclick="showimgFn('.$image_id.')" class="showimg" src="'.$data.'">';
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
        halt(config());
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

    /*
     *   测试方法
     */
    public function test_function()
    {
        $user_info = Db::table("tp_member")->where("user_name ='czx' ")->find();
        halt($user_info);
        $list = Db::query("select * from tp_member where user_name='czx' ");

        /*
         *   这个方法是输出截停halt           dump不是tp3.2的版本了， 他不会截停
         */
        halt($list);
        return 222;
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
