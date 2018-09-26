<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>荣耀医者 - H5 接口文档</title>

<!-- Bootstrap core CSS -->
<link href="css/api/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/api/default.css">

<style type="text/css">
html,body{ overflow:hidden;height: 100%; }
.content{background-color: rgba(255, 255, 255, 0.92);height: 100%;}
h2{ margin:0; padding:25px 0; }
#sidebar{ overflow-y:auto; padding-right:20px; padding-left:20px; padding-top:20px; box-shadow:2px 2px 3px rgba(223, 223, 223, 0.59); }
#result .input-group{  margin-right:50px; box-shadow:2px 2px 3px rgba(223, 223, 223, 0.59); margin-bottom:10px; }
.item-result{ margin-bottom:15px; margin-right:50px; }
.item-result pre{ overflow-y:scroll; background-color: rgba(221, 221, 221, 0.22); box-shadow:2px 2px 3px rgba(223, 223, 223, 0.59); clear:both;  margin-bottom:20px;}
.item-result h5{float: right;
    position: relative;
    margin-bottom: -28px;
    margin-right: 16px;
    background-color: rgba(197, 197, 197, 0.28);
    padding: 6px 14px;
    color: green;  }
.item-doc{ border:1px solid #ccc; padding:0 10px; margin-bottom:5px; border-radius:3px; box-shadow:2px 2px 3px rgba(223, 223, 223, 0.59); }
.item-doc form{ padding-bottom:10px; display:none; }
.item-doc em{ color:red; }
.item-doc .input-group{ margin-bottom:10px; width:95%; }
.item-doc .input-group-addon{ width:45%; text-align:right;}
.item-doc h5{  cursor:pointer; }
.item-doc h5 .link{ color:#2a6496; }
.alert-warning{ background-color: rgba(245, 236, 189, 0.52); }
</style>
</head>
<body>
<div class="content">

<h2 class="text-center">荣耀医者 - H5 接口文档</h2>

<div class="row">
    <div class="col-md-3">
        <ul class="nav sidebar-nav" id="sidebar">
            <li>
                <div class="item-doc">
                    <h5>说明：</h5>
                    <p>
                        [code]  <br />
                        =200  &nbsp;&nbsp;   ok<br />
                        =500   &nbsp;&nbsp;     错误
                    </p>

                </div>
            </li>
            <li>
	            <h3>荣耀医者</h3>
                <div class="item-doc">
                    <h5>时间限制列表: <small class="link">/api/timeSettingList</small></h5>
                    <form role="form" action="/api/timeSettingList">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>图形验证码: <small class="link">/api/doctor/showCaptcha</small></h5>
                    <form role="form" action="/api/doctor/showCaptcha">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>测试环境设置cookie: <small class="link">/api/doctor/testSetCookie</small></h5>
                    <form role="form" action="/api/doctor/testSetCookie">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>user_token</span><input name="user_token" placeholder="user_token,从用户登录接口获取" value="" type="text" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>用户登录: <small class="link">/api/doctor/userLogin</small></h5>
                    <form role="form" action="/api/doctor/userLogin">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>phone_number</span><input name="phone_number" placeholder="手机号" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>pic_code</span><input name="pic_code" placeholder="图形验证码" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>sms_code</span><input name="sms_code" placeholder="短信验证码" value="" type="text" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>用户退出: <small class="link">/api/doctor/userLogout</small></h5>
                    <form role="form" action="/api/doctor/userLogout">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>报名注册: <small class="link">/api/doctor/signUp</small></h5>
                    <form role="form" action="/api/doctor/signUp">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>name</span><input name="name" placeholder="姓名" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>sex</span><input name="sex" placeholder="性别，男1，女0" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>age</span><input name="age" placeholder="年龄" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>wanted_award</span><input name="wanted_award" placeholder="报名奖项" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>working_year</span><input name="working_year" placeholder="工作年限" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>hospital_id</span><input name="hospital_id" placeholder="所属医院id" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>hospital_name</span><input name="hospital_name" placeholder="所属医院名称" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>department</span><input name="department" placeholder="所属科室" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>job_title</span><input name="job_title" placeholder="专业职称（格式为标准json）" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>medical_certificate_no</span><input name="medical_certificate_no" placeholder="医师资格证号" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>email</span><input name="email" placeholder="邮箱" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>full_face_photo</span><input name="full_face_photo" placeholder="免冠照片" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">doctor_other_info</span><input name="doctor_other_info" placeholder="报名医生其他详细信息，比如医患故事、个人荣誉等等（格式为标准json）" value="" type="text" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>团体奖报名注册: <small class="link">/api/doctor/teamSignUp</small></h5>
                    <form role="form" action="/api/doctor/teamSignUp">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>name</span><input name="name" placeholder="姓名" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>wanted_award</span><input name="wanted_award" placeholder="报名奖项" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>hospital_id</span><input name="hospital_id" placeholder="所属医院id" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>hospital_name</span><input name="hospital_name" placeholder="所属医院名称" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>department</span><input name="department" placeholder="所属科室" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>email</span><input name="email" placeholder="邮箱" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>full_face_photo</span><input name="full_face_photo" placeholder="免冠照片" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">doctor_other_info</span><input name="doctor_other_info" placeholder="报名医生其他详细信息，比如医患故事、个人荣誉等等" value="" type="text" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>报名信息详情: <small class="link">/api/doctor/userSignUpInfoDetail</small></h5>
                    <form role="form" action="/api/doctor/userSignUpInfoDetail">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>id</span><input name="id" placeholder="奖项id" value="" type="text" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>报名信息修改: <small class="link">/api/doctor/signUpInfoEdit</small></h5>
                    <form role="form" action="/api/doctor/signUpInfoEdit">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>id</span><input name="id" placeholder="id" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">name</span><input name="name" placeholder="姓名" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">sex</span><input name="sex" placeholder="性别，男1，女0" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">age</span><input name="age" placeholder="年龄" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">wanted_award</span><input name="wanted_award" placeholder="报名奖项" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">working_year</span><input name="working_year" placeholder="工作年限" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">hospital_id</span><input name="hospital_id" placeholder="所属医院id" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">hospital_name</span><input name="hospital_name" placeholder="所属医院名称" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">department</span><input name="department" placeholder="所属科室" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">job_title</span><input name="job_title" placeholder="专业职称（格式为标准json）" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">medical_certificate_no</span><input name="medical_certificate_no" placeholder="医师资格证号" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">email</span><input name="email" placeholder="邮箱" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">full_face_photo</span><input name="full_face_photo" placeholder="免冠照片" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">doctor_other_info</span><input name="doctor_other_info" placeholder="报名医生其他详细信息，比如医患故事、个人荣誉等等（格式为标准json）" value="" type="text" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>未报名、审核中、已通过奖项列表: <small class="link">/api/doctor/userAwardList</small></h5>
                    <form role="form" action="/api/doctor/userAwardList">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>医院列表: <small class="link">/api/doctor/hospitalList</small></h5>
                    <form role="form" action="/api/doctor/hospitalList">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <div class="input-group">
                            <span class="input-group-addon">district_id</span><input name="district_id" placeholder="地区id" value="" type="text" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>奖项配置: <small class="link">/api/doctor/configAward</small></h5>
                    <form role="form" action="/api/doctor/configAward">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>职称配置: <small class="link">/api/doctor/configJobTitle</small></h5>
                    <form role="form" action="/api/doctor/configJobTitle">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </li>
            <li>
                <h3>投票</h3>
                <div class="item-doc">
                    <h5>用户投票: <small class="link">/api/vote/userVote</small></h5>
                    <form role="form" action="/api/vote/userVote">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>candidate_id</span><input name="candidate_id" placeholder="候选人id" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>award_id</span><input name="award_id" placeholder="奖项id，可参考 ·奖项配置· 接口" value="" type="text" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>投票列表: <small class="link">/api/vote/candidateVoteList</small></h5>
                    <form role="form" action="/api/vote/candidateVoteList">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <div class="input-group">
                            <span class="input-group-addon"><em>*</em>award_id</span><input name="award_id" placeholder="奖项id，可参考 ·奖项配置· 接口" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">doctor_name</span><input name="doctor_name" placeholder="医生姓名" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">cur_page</span><input name="cur_page" placeholder="当前页数" value="" type="text" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">length</span><input name="length" placeholder="每页显示条数" value="" type="text" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="item-doc">
                    <h5>专家奖项列表及剩余票数: <small class="link">/api/vote/expertAwardListWithVotes</small></h5>
                    <form role="form" action="/api/vote/expertAwardListWithVotes">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </li>

         </ul>
     </div>

    <div class="col-md-9" id="result">
        <div class="input-group">
            <span class="input-group-addon">请求URL</span><input id="url" value="" type="text" class="form-control" onclick="this.select()" />
        </div>
        <div class="item-result">
            <h5>json</h5>
            <pre></pre>
        </div>
        <div class="item-result">
            <h5>文本</h5>
            <pre></pre>
        </div>
    </div>
</div>


</div>
<script src="js/api/jquery-1.9.1.min.js"></script>
<script src="js/api/jquery.form.js"></script>
<script src="js/api/highlight.pack.js"></script>
<!--<script src="static/lib/bootstrap/js/bootstrap.min.js"></script>-->
<script>
    var JsonUti = {
        //定义换行符
        n: "\n",
        //定义制表符
        t: "   ",
        //转换String
        convertToString: function(obj) {
            return JsonUti.__writeObj(obj, 1);
        },
        //写对象
        __writeObj: function(obj //对象
                , level //层次（基数为1）
                , isInArray) { //此对象是否在一个集合内
            //如果为空，直接输出null
            if (obj == null) {
                return "null";
            }
            //为普通类型，直接输出值
            if (obj.constructor == Number || obj.constructor == Date || obj.constructor == String || obj.constructor == Boolean) {
                var v = obj.toString();
                var tab = isInArray ? JsonUti.__repeatStr(JsonUti.t, level - 1) : "";
                if (obj.constructor == String || obj.constructor == Date) {
                    //时间格式化只是单纯输出字符串，而不是Date对象
                    return tab + ("\"" + v + "\"");
                }
                else if (obj.constructor == Boolean) {
                    return tab + v.toLowerCase();
                }
                else {
                    return tab + (v);
                }
            }
            //写Json对象，缓存字符串
            var currentObjStrings = [];
            //遍历属性
            for (var name in obj) {
                var temp = [];
                //格式化Tab
                var paddingTab = JsonUti.__repeatStr(JsonUti.t, level);
                temp.push(paddingTab);
                //写出属性名
                temp.push("\"" + name + "\" : ");
                var val = obj[name];
                if (val == null) {
                    temp.push("null");
                }
                else {
                    var c = val.constructor;
                    if (c == Array) { //如果为集合，循环内部对象
                        temp.push(JsonUti.n + paddingTab + "[" + JsonUti.n);
                        var levelUp = level + 2; //层级+2
                        var tempArrValue = []; //集合元素相关字符串缓存片段
                        for (var i = 0; i < val.length; i++) {
                            //递归写对象
                            tempArrValue.push(JsonUti.__writeObj(val[i], levelUp, true));
                        }
                        temp.push(tempArrValue.join("," + JsonUti.n));
                        temp.push(JsonUti.n + paddingTab + "]");
                    }
                    else if (c == Function) {
                        temp.push("[Function]");
                    }
                    else {
                        //递归写对象
                        temp.push(JsonUti.__writeObj(val, level + 1));
                    }
                }
                //加入当前对象“属性”字符串
                currentObjStrings.push(temp.join(""));
            }
            return (level > 1 && !isInArray ? JsonUti.n: "") //如果Json对象是内部，就要换行格式化
                    + JsonUti.__repeatStr(JsonUti.t, level - 1) + "{" + JsonUti.n //加层次Tab格式化
                    + currentObjStrings.join("," + JsonUti.n) //串联所有属性值
                    + JsonUti.n + JsonUti.__repeatStr(JsonUti.t, level - 1) + "}"; //封闭对象
        },
        __isArray: function(obj) {
            if (obj) {
                return obj.constructor == Array;
            }
            return false;
        },
        __repeatStr: function(str, times) {
            var newStr = [];
            if (times > 0) {
                for (var i = 0; i < times; i++) {
                    newStr.push(str);
                }
            }
            return newStr.join("");
        }
    };
</script>
<script>
$(function(){
    var $result=$('#result')
            , $sidebar=$('#sidebar')
            , $children=$result.children()
            , $pre=$children.find('pre')
            , offsetTop=$result.offset().top;
    $(window).resize(function(){
        var height=(document.documentElement.clientHeight-offsetTop-140)/2;
        $pre.eq(0).height( height*0.7*2 );
        $pre.eq(1).height( height*0.3*2 );
        $sidebar.height( document.documentElement.clientHeight-offsetTop-35 );
    }).trigger('resize');

    function getUrl( $form ){
        return $form.attr('action')
    }
    $('form').submit(function(e){
        e.stopPropagation();
        var $this=$(this);
        var url=getUrl($this);
        $pre.html('');
        $('#url').val( window.location.origin + url );
        $this.ajaxSubmit({
            type:'post',
            url:url,
			dataType: 'text',
            success:function( jsonText ){
                var text=jsonText;
                try{
                    var json=$.parseJSON( jsonText );
                    jsonText=JsonUti.convertToString(json);
                }catch(e){}
                $pre.eq(0).html( jsonText );
                $pre.eq(1).html( text );
                hljs.highlightBlock( $pre[0] );
                hljs.highlightBlock( $pre[1] );
            },
            error:function(XMLHttpRequest, textStatus, errorThrown){
                $pre.html( '' );
                $pre.eq(0).html( 'status:'+XMLHttpRequest.status+'<br><br>'+XMLHttpRequest.responseText );
            }
        });
        return false;
    });

    $('#sidebar h5').click(function(){
        $(this).next('form').toggle();
    })
});
</script>
</body>
</html>

