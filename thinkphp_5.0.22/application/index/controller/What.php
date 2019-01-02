<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 9:47
 */
namespace app\index\controller;
use GatewayClient\Gateway;
use think\cache\driver\Redis;
use think\Config;
use think\Controller;
use think\Request;
use think\Session;


class What extends Controller{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $user_info = Session::get("user_info");
        if(!$user_info) $this->error("带佬请不要翻墙");
    }

    public function index()
    {
        halt(Session::get("user_info"));
    }

    /*
     *   绑定客户端
     */
    public function bind_client()
    {
        // 设置GatewayWorker服务的Register服务ip和端口
        $ip_addr = config('workermanRegisterAddress');

        Gateway::$registerAddress = $ip_addr;

        $user_info = Session::get("user_info");
        // 假设用户已经登录，用户uid和群组id在session中
        $uid      = $user_info['user_id'];
        //$group_id = $user_info['group'] ? $user_info['group'] : 1;
        $client_id = input('client_id');

        /*
         *  client_id与uid绑定   绑在一起  以后可以直接用user_id 来发送数据了
         *  页面一开始就去连接wm服务器 ， wm保存了client_id 作为连接的凭证 ，  所以请求不同的wm服务器，将会报错
         */
        Gateway::bindUid($client_id, $uid);


        // 加入某个群组（可调用多次加入多个群组）
        //Gateway::joinGroup($client_id, $group_id);
        Config::set("default_return_type" , "json");
        return ["status"=>"200","msg"=>"成功"];
    }


    /*
     *   向workerman发送消息
     */
    public function send_message()
    {
        $content = input("content");

        $send_param = $this->init_send_data($content);

        $result = $this->send_message_to_all($send_param);

        Config::set("default_return_type" , "json");

        if($result){
            return ["status"=>"200","msg"=>"成功"];
        }else{
            return ["status"=>"-1","msg"=>"发送失败"];
        }
        /*
            $uid      = $_SESSION['uid'];
            $group_id = $_SESSION['group'];
            向任意uid的网站页面发送数据
            Gateway::sendToUid($client_id, $message);
            Gateway::joinGroup($client_id, 1);
             向任意群组的网站页面发送数据
            Gateway::sendToGroup(1, json_encode($data));
        */
    }

    /**
     * return : 聊天上传图片
     * author : czx
     * date:2018/12/29
     */
    public function what_upload_image()
    {
        //file为表单name值
        $file = $this->request->file('file');
        /*
         *  验证图片类型 , 大小
         */
        $info = $file->validate(['size'=>1024*1024,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');

        if ($info) {
            $filename = '/uploads/' . str_replace('\\', '/', $info->getSaveName());
            $result = [
                "status"     => "200",
                "msg"      => "上传成功",
                "filename" => $filename
            ];
           $send_param = $this->init_send_data($filename , "image");
           $send_result = $this->send_message_to_all($send_param);
           if($send_result)  return json($result);

        }

        $msg = $filename ? "发送失败" : $file->getError();

        $result = ['status' => -1, 'msg'  => $msg];

        return json($result);
    }

    /*
     *   发送消息通用方法
     */
    private function send_message_to_all($send_param){
        $ip_addr = config('workermanRegisterAddress');
        try{
            Gateway::$registerAddress = $ip_addr;
            Gateway::sendToAll(json_encode($send_param));
            $this->save_message_by_redis($send_param);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

    /*
     *   保存消息到redis中
     */
    private function save_message_by_redis($data){
        if(!$data) return true;
        $redis_server = new Redis(config('redis_conf'));
        $message  = $redis_server->get("message_list");
        if($message){
            $message_list = json_decode($message , true);
            $message_list[] = $data;
            $redis_server->set('message_list' , json_encode($message_list));
        }else{
            $send_data[] = $data;
            $redis_server->set('message_list' , json_encode($send_data));
        }
    }

    /*
     *   初始化发送数据
     */
    private function init_send_data($content , $type='say'){
        $send_param['content'] = $content;
        $send_param['type'] = $type;
        $send_param['time'] = time();
        $user_info = Session::get("user_info");
        $send_param['user_id'] = $user_info['user_id'];
        $send_param['head_image'] = $user_info['head_image'];
        $send_param['user_name'] = $user_info['user_name'];
        return $send_param;
    }
    /*
     *  加载 client包
     *  D:\phpWorkSpace\phpfreemarker\Tp5.0\thinkphp_5.0.22>composer require workerman/gatewayclient
     */
}