function send(headSrc, str) {
    var html = "<div class='send'><div class='msg'><img src=" + headSrc + " />" +
        "<div class='p'><i class='msg_input'></i>" + str + "</div></div></div>";
    console.log(html);
    upView(html);
}

function show(headSrc, str) {
    //异步请求
    var html = "<div class='show'><div class='msg'><img class='headSrc' src=" + headSrc + " />" +
        "<div class='p'><i class='msg_input'></i>" + str + "</div></div></div>";
    upView(html);
}
function upView(html) {
    $('.message').append(html);
    $('body').animate({
        scrollTop: $('.message').outerHeight() - window.innerHeight
    }, 200)
}
function sj() {
    return parseInt(Math.random() * 10)
}
$(function() {
    $('body').animate({
        scrollTop: $('.message').outerHeight() - window.innerHeight
    }, 200)
    $('.footer').on('keyup', '.easyEditor', function(e) {
        if ($(this).text().length > 0&&$(this).text().replace(/\s*/g,"")!='') {
            $('.fasong').css('background', '#114F8E').prop('disabled', true);
        } else {
            $('.fasong').css('background', '#ddd').prop('disabled', false);
            $('.easyEditor').html('')
        }
    })
      $("#ipt_img").change(function() {
        var $file = $(this);
        var fileObj = $file[0];
        var windowURL = window.URL || window.webkitURL;
        var dataURL;
        var html='';
        if(fileObj && fileObj.files && fileObj.files[0]){
            dataURL = windowURL.createObjectURL(fileObj.files[0]);
        }else{
            dataURL = $file.val();
        }

        var formData =new FormData();
        formData.append('file', fileObj.files[0]);  //添加图片信息的参数
        formData.append('sizeid',123);  //添加其他参数
        $.ajax({
            url: upload_image_url,
            type: 'POST',
            data: formData ,
            processData: false, // 告诉jQuery不要去处理发送的数据
            contentType: false, // 告诉jQuery不要去设置Content-Type请求头
            success: function (data) {
                if(data && data.status==200){
                    var readerID=parseInt(Math.random()*10000000000000);
                    html = "<div class='show'><div class='msg'><img class='headSrc' src='"+headSrc+"' />" +
                        "<div class='p'><img id="+readerID+"  onclick='showimgFn("+readerID+")' class='showimg' src="+data.filename+" ></div></div></div>";
                    $('.message').append(html);
                }else{
                    alert("上传失败");
                }

                $('body').animate({
                    scrollTop: $('.message').outerHeight()
                }, 200)
            },
            error: function (data) {
                console.log(data)
            }
        })
    });
    var Appflay=getUrlParam('is_app');
    if(Appflay){
        $('.header').css('display','none');
        $('.message').css('paddingTop','0.3rem');
    }
})
$(".easyEditor").on('paste', contentHandler);
function contentHandler(e){
     $('.fasong').css('background', '#114F8E').prop('disabled', true);
    //此处不能获取当前的输入修改
}
function getUrlParam(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null) return unescape(r[2]); return null;
}
function showimgFn(imgId){
   var imgSrc=$('#'+imgId).attr('src');
   $('.showImgModal img').attr('src',imgSrc);
    $('.showImgModal').css('display','flex');
}
var timestamp=new Date().getTime();
function SubmitFn(){
    $('.footer .fasong').click(function() {
            SubmitFn2()
    });

}
function SubmitFn2(){
        if(! $('.fasong').prop('disabled')){return ;}
        var str = $('.easyEditor').html().replace(/"/g, "'");
        str =str.replace(/<(?!img).*?>/g, "");
        if ( $('.fasong').prop('disabled')&&str!='') {
            var _nstr = $('.easyEditor').html().replace(/"/g, "'");
            $.post(send_url, {content: _nstr}, function(data){
                console.log(data);
                if(data && data.status==200){
                    show(headSrc, _nstr);
                    $('.easyEditor').html('')
                }
            }, 'json');

        }
}
var ws, name, client_list={};
function connect(){
    // ws = new WebSocket("ws://47.106.221.85:8080");
    ws = new WebSocket("ws://127.0.0.1:7272");
    console.log(ws);
    ws.onopen = SubmitFn;
    ws.onmessage = onmessage;
    ws.onclose = function() {
        console.log("连接关闭，定时重连");
        connect();
    };
    ws.onerror = function() {
        console.log("出现错误");
    };
}



// 服务端发来消息时
function onmessage(e)
{
    console.log(e.data);
    var data = JSON.parse(e.data);
    switch(data['type']){
        // 服务端ping客户端
        case 'ping':
            ws.send('{"type":"pong"}');
            break;;
        // 登录 更新用户列表
        case 'init':
            //获取当前用户id     || !data['client_list']不能加上，因为有人登陆后这个列表的数据会刷新带有数据，所以
            $.post(bind_url, {client_id: data.client_id}, function(data){}, 'json');
            break;
        case 'say':
            //console.log(data);
            if(data['user_id'] != user_id){
                $real_content =  escape2Html(data['content']);
                send(data['head_image'], $real_content);
            }
            //say(data['from_client_id'], data['from_client_name'], data['content'], data['time']);
            break;
        case 'image':
            if(data['user_id'] != user_id){
                var readerID=parseInt(Math.random()*10000000000000);
                $real_content =  data['content'];
                var _nstr = "<img id="+readerID+"  onclick='showimgFn("+readerID+")' class='showimg' src='"+$real_content+"'>";
                send(data['head_image'], _nstr);
            }

        // 用户退出 更新用户列表
        // case 'logout':
        //     //{"type":"logout","client_id":xxx,"time":"xxx"}
        //     say(data['from_client_id'], data['from_client_name'], data['from_client_name']+' 退出了', data['time']);
        //     delete client_list[data['from_client_id']];
        //     flush_client_list();
    }
}

connect();


$("body").keydown(function() {
    if (event.keyCode == "13") {
        SubmitFn2();
    }
})

//HTML标签反转义（&lt; -> <）
function escape2Html(str) {
    var arrEntities={'lt':'<','gt':'>','nbsp':' ','amp':'&','quot':'"'};
    return str.replace(/&(lt|gt|nbsp|amp|quot);/ig,function(all,t){
        return arrEntities[t];
    });
}
