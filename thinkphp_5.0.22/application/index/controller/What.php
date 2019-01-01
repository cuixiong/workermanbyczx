<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 9:47
 */
namespace app\index\controller;
use GatewayClient\Gateway;
use think\Config;
use think\Controller;
use think\Request;
use think\Session;

class What extends Controller{

    /*
     *  绑定workerman
     */
    public function index()
    {

       return $this->fetch();

    }

    /*
     *   绑定客户端
     */
    public function bind_client()
    {
        //mvc后端uid绑定代码片段
        // 设置GatewayWorker服务的Register服务ip和端口
        $ip_addr = config('workermanRegisterAddress');

        Gateway::$registerAddress = $ip_addr;
        $user_info = Session::get("user_info");
        $user_info = $user_info ? $user_info[0] : NULL ;
        // 假设用户已经登录，用户uid和群组id在session中
        $uid      = $user_info['user_id'];
        //$group_id = $user_info['group'] ? $user_info['group'] : 1;
        $client_id = input('client_id');
    //echo $uid . '----'.$client_id; exit;
        // client_id与uid绑定   绑在一起  以后可以直接用user_id 来发送数据了
        Gateway::bindUid($client_id, $uid);


        // 加入某个群组（可调用多次加入多个群组）
        //Gateway::joinGroup($client_id, $group_id);
        Config::set("default_return_type" , "json");
        //Session::set('client_id',$client_id);

        return ["status"=>"200","msg"=>"成功"];
    }


    /*
     *   向workerman发送消息
     */
    public function send_message()
    {
        $user_info = Session::get("user_info");
        $user_info = $user_info ? $user_info[0] : NULL;
        if(!$user_info)  return "错误";
        $content = input("content");

        $ip_addr = config('workermanRegisterAddress');

        $data = ['msg'=>$content , "type"=>'say' , 'user_info'=>$user_info];
//        $uid      = $_SESSION['uid'];
//        $group_id = $_SESSION['group'];
        Gateway::$registerAddress = $ip_addr;
        Gateway::sendToAll(json_encode($data));
        //Gateway::sendToClient($client_id, json_encode($data));
        /*
            向任意uid的网站页面发送数据
            Gateway::sendToUid($client_id, $message);
            Gateway::joinGroup($client_id, 1);
             向任意群组的网站页面发送数据
            Gateway::sendToGroup(1, json_encode($data));
        */

        Config::set("default_return_type" , "json");
        return ["status"=>"200","msg"=>"成功"];
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
            $user_info = Session::get("user_info");
            $user_info = $user_info ? $user_info[0] : NULL;
            $ip_addr = config('workermanRegisterAddress');
            Gateway::$registerAddress = $ip_addr;
            $data = ['msg'=>$filename , "type"=>'image' , 'user_info'=>$user_info];
            Gateway::sendToAll(json_encode($data));
        } else {
            $result = [
                'status' => -1,
                'msg'  => $file->getError()
            ];
        }
       return json($result);
    }



    /*
     *  加载 client包
     *  D:\phpWorkSpace\phpfreemarker\Tp5.0\thinkphp_5.0.22>composer require workerman/gatewayclient
     */
}