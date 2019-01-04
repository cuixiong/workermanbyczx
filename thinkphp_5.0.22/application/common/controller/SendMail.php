<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/4
 * Time: 11:56
 *
     禁止访问模块
    'deny_module_list'       => ['common'],
    这个模块  用于内部访问
 */

namespace app\common\controller;
use think\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use think\Validate;
class SendMail extends Controller
{
    public function test()
    {
        //公共模块不给直接访问
        return "111";
    }

    public function send_register_mail()
    {
        $input_data = input('param.') ;
        $rule = [
            'email' => 'email',
        ];

        $msg = [
            'email'        => '邮箱格式错误',
        ];

        $data = [
            'email' => $input_data['email'],
        ];

        $validate = new Validate($rule, $msg);
        $result   = $validate->check($data);
        if(!$result) $this->error("验证失败");

        $input_data['from_name'] = 'czxwebim';
        $input_data['authorization_url'] = "webim.hellocuizhixiong97.cn?token=fadskjbfkjbvkjbfkjdbvnjfdnvjdnfsvkjnskjn&timestram=1925131";
        $input_data['host_name'] = "webim.hellocuizhixiong97.cn";
        $input_data['time'] = date('Y-m-d H:i' , time());
        $emial_model_html =$this->build_email_register_html($input_data);

        $title = "webimczx账号激活";
        $result = $this->sendMail($input_data['email'] ,$title , $emial_model_html);

        return "发送成功";
    }

    /*
     *   构建邮件注册的html
     */
    private function build_email_register_html($input_data){
        $mystring = <<<EOT
         <div>
<span class="genEmailNicker">

</span>
<br>
<span class="genEmailContent">
          <br>
尊敬的用户 ：
<br>
<br>
&nbsp;&nbsp; &nbsp; &nbsp; 您好！恭喜您注册成为{$input_data['from_name']}账号。
<br>
<br>
&nbsp;&nbsp; &nbsp; &nbsp; 这是一封注册认证邮件，请点击以下链接确认：
<br>
&nbsp;&nbsp; &nbsp; &nbsp; <a href="{$input_data['authorization_url']}" target="_blank" rel="noopener">{$input_data['authorization_url']}</a> 
<br>
<br>
&nbsp;&nbsp; &nbsp; &nbsp; 如果链接不能点击，请复制地址到浏览器，然后直接打开。
<br>
<br>
&nbsp;&nbsp; &nbsp; &nbsp; 上述链接48小时内有效。如果激活链接失效，请您登录网站
<a target="_blank" href="{$input_data['host_name']}" rel="noopener"> {$input_data['host_name']}</a>
重新申请认证。
<br>
<br>
&nbsp;&nbsp; &nbsp; &nbsp; 感谢您注册{$input_data['from_name']}账号！
<br>
<br>
&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; {$input_data['from_name']}项目组
<br>
<br>
&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; {$input_data['time']}
</span>
<br>
<span class="genEmailTail">

</span>
</div>
EOT;
    return $mystring;
    }

    /*
     *  * 发送邮件方法
     *  @param string $to：接收者邮箱地址
     *  @param string $title：邮件的标题
     *  @param string $content：邮件内容
     *  @return boolean  true:发送成功 false:发送失败
     */
    private function sendMail($to , $title, $content){
        /*理解邮件类
            授权码是QQ邮箱推出的，用于登录第三方客户端的专用密码。
            适用于登录以下服务：POP3/IMAP/SMTP/Exchange/CardDAV/CalDAV服务。
            温馨提醒：为了你的帐户安全，更改QQ密码以及独立密码会触发授权码过期，需要重新获取新的授权码登录。
            邮箱	  POP3服务器（端口995）	SMTP服务器（端口465或587）
            qq.com	   pop.qq.com	             smtp.qq.com
            SMTP服务器需要身份验证。
            却报错Extension missing: openssl   打开php扩展 extension=php_openssl.dll
        */
        $mail_conf = config('mail');
        //实例化PHPMailer核心类
        $mail = new PHPMailer();


        /*
         *  SMTP设置
         */
        $mail->SMTPDebug = $mail_conf['SMTP_DEBUG'];    //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();
        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth=true;
        //链接qq域名邮箱的服务器地址
        $mail->Host = $mail_conf['SMTP_HOST'];
        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = $mail_conf['SMTP_SECURE'];
        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        $mail->Port = $mail_conf['SMTP_PORT'];


        /*
         *  发件人信息设置
         */
        $mail->FromName = $mail_conf['FROM_NAME'];
        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username = $mail_conf['USERNAME'];
        //smtp登录的密码 使用生成的授权码 你的最新的授权码
        $mail->Password = $mail_conf['PASSWORD'];
        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From = $mail_conf['FROM'];
        //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
        $mail->Hostname = $mail_conf['FROM_HOST'];


        /*
         *  其他配置
         */
        $mail->Helo = 'Hello smtp.qq.com Server';  //设置smtp的helo消息头 这个可有可无 内容任意
        //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $mail->CharSet = $mail_conf['CHARSET'];
        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);


        /*
         *  收件人信息与数据设置
         */
        $mail->addAddress($to,'czxwebim'); //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        //添加多个收件人 则多次调用方法即可
        // $mail->addAddress('xxx@qq.com','lsgo在线通知');
        //添加该邮件的主题
        $mail->Subject = $title;
        //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
        $mail->Body = $content;

        /*
         *  为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
         *  $mail->addAttachment('./static/home/image/2.jpg','mm.jpg');
         *  同样该方法可以多次调用 上传多个附件
         $  mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');
         */


        $status = $mail->send();

        return $status;
    }
}