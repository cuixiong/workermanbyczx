<?php
/*
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 10:52
 * 定时任务控制器
 *
 * 请求方式    先进入 单入口文件中
 *   php index.php index/Crontab/message_save_db_by_redis
 */

namespace app\index\controller;
use think\cache\driver\Redis;
use think\Controller;
use think\Db;
use think\Request;

class Crontab extends Controller
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $sapi_type  =  php_sapi_name ();
        if ( substr ( $sapi_type ,  0 ,  3 ) !=  'cli' ) {
            $this->error("带佬这是我们内部请求的");
        }
    }

    /*
     *  redis数据导入数据库中
     */
    public function message_save_db_by_redis()
    {
        $redis_server = new Redis(config('redis_conf'));
        $message_list = $redis_server->get("message_list");
        $date_format = date("Y-m-d H:i:s" , time());
        if($message_list){
           $message_list = json_decode($message_list  , true);
           $result = Db::table('tp_message')->insertAll($message_list);
           //销毁缓存
           if($result) $redis_server->set("message_list" ,"");
           return "result : {$result}    date_time".$date_format."\n";
        }
        return "result : 0    date_time".$date_format."\n";
    }
}