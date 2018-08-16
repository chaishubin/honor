<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>荣耀医者 - WEB 接口文档</title>

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

    <h2 class="text-center">荣耀医者 - WEB 接口文档</h2>

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
                        <h5>报名信息列表: <small class="link">/api/doctor/signUpList</small></h5>
                        <form role="form" action="/api/doctor/signUpList">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                            <div class="input-group">
                                <span class="input-group-addon">name</span><input name="name" placeholder="姓名" value="" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">job_title</span><input name="job_title" placeholder="专业职称" value="" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">hospital_name</span><input name="hospital_name" placeholder="所属医院名称" value="" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">department</span><input name="department" placeholder="所属科室" value="" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">status</span><input name="status" placeholder="状态，1待审核，2已通过，3未通过" value="" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">cur_page</span><input name="cur_page" placeholder="当前页数" value="" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">length</span><input name="length" placeholder="条数" value="" type="text" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                    <div class="item-doc">
                        <h5>报名信息详情: <small class="link">/api/doctor/signUpInfoDetail</small></h5>
                        <form role="form" action="/api/doctor/signUpInfoDetail">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                            <div class="input-group">
                                <span class="input-group-addon"><em>*</em>id</span><input name="id" placeholder="审核信息id，对应列表接口中的id" value="" type="text" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                    <div class="item-doc">
                        <h5>审核内容列表: <small class="link">/api/doctor/signUpInfoReviewList</small></h5>
                        <form role="form" action="/api/doctor/signUpInfoReviewList">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                            <div class="input-group">
                                <span class="input-group-addon"><em>*</em>info_id</span><input name="info_id" placeholder="审核信息id，对应列表接口中的id" value="" type="text" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                    <div class="item-doc">
                        <h5>报名信息审核: <small class="link">/api/doctor/signUpInfoReview</small></h5>
                        <form role="form" action="/api/doctor/signUpInfoReview">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                            <div class="input-group">
                                <span class="input-group-addon"><em>*</em>info_id</span><input name="info_id" placeholder="审核信息id，对应列表接口中的id" value="" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><em>*</em>status</span><input name="status" placeholder="审核状态，1待审核，2已通过，3未通过" value="" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><em>*</em>content</span><input name="content" placeholder="审核内容" value="" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><em>*</em>review_way</span><input name="review_way" placeholder="审核方式，1单条审核，2批量审核" value="" type="text" class="form-control">
                            </div>
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

