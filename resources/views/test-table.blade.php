<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>医院</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div>
                    二级地区名，如兰州市、朝阳区
                    <input type="text" name="district" id="district">
                </div>

                <div>
                    <table border  id="title">
                        <tr id="title">
                            <th>地区名称</th>
                            <th>完整名称</th>
                            <th>地区编号</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <script src="https://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
        <script>
            $(document).ready(function () {
                 $("#district").blur(function () {
//                     location.reload();

                     var district = $("#district").val();

                    $.ajax({
                        url:"http://192.168.1.161:8016/api/doctor/testHospital",
                        type:"post",
                        data:{
                            "district_name":district
                        },
                        success:function (res) {

                            console.log(res.data)
                            var result = res.data;
                            var str = null;
                            if (result){
                                str = `<tr><th>地区名称</th><th>地区名称</th><th>地区编号</th></tr>`
                                result.forEach(function (item) {
                                    str += `<tr><td>${item.district_name}</td><td>${item.district_mergershortname}</td><td>${item.district_id}</td></tr>`
                                })
                                $("#title").html(str)
                            }

                        }
                    })
                 })
            })
        </script>
    </body>
</html>
