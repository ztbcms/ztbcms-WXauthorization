
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<style>
    .pop_nav{
        padding: 0px;
    }
    .pop_nav ul{
        border-bottom:1px solid #266AAE;
        padding:0 5px;
        height:25px;
        clear:both;
    }
    .pop_nav ul li.current a{
        border:1px solid #266AAE;
        border-bottom:0 none;
        color:#333;
        font-weight:700;
        background:#F3F3F3;
        position:relative;
        border-radius:2px;
        margin-bottom:-1px;
    }

</style>
<div class="wrap J_check_wrap">
    <Admintemplate file="Common/Nav"/>
    <div class="pop_nav">
        <ul class="J_tabs_nav">
            <li class="current"><a href="javascript:;;">代码上传必备参数</a></li>
        </ul>
    </div>
    <form class="J_ajaxForms" name="myform" id="myform" action="{:U("Management/wx_upload_code",array('authorizer_appid'=> $authorizer_appid))}" method="post">
    <div class="J_tabs_contents">
        <div>
            <div class="h_a">代码上传必备参数</div>
            <div class="table_full">
                <table width="100%" class="table_form ">
                    <tr>
                        <th>代码库中的代码模版ID(template_id)（必填）：</th>
                        <td><input type="text" name="template_id" id="catname" class="template_id" value=""></td>
                    </tr>
                    <tr>
                        <th>授权调试的 AppID（必填）：</th>
                        <td><input type="text" name="template_ids" id="catname" class="template_ids" value=""><span><br>例如开发者在此处填写的是 wxf9c4501a76931b33 那么在 extEnable 为真的情况下，后续的开发逻辑都会基于 wxf9c4501a76931b33 来运行。</span></td>
                    </tr>

                    <tr>
                        <th>代码版本号，开发者可自定义(user_version)（必填）：</th>
                        <td><input type="text" name="user_version" id="seturl" class="input user_version" value=""></td>
                    </tr>
                    <tr>
                        <th>代码描述，开发者可自定义(user_desc) （必填）：</th>
                        <td><input type="text" name="user_desc" id="seturl" class="input length_6 user_desc" value=""></td>
                    </tr>
                    <tr>
                        <th>自定义属性（ 字段是开发自定义的数据字段，在小程序中可以通过 wx.getExtConfigSync 或者 wx.getExtConfig 获取到这些配置信息。两行选项有一个空未填直接取消整行信息）：</th>
                        <td>
                        <div style="margin-left: 62px;"  class="row cl product-color">
                            <div style="margin-top: 10px;" class="formControls col-xs-8 col-sm-9">
                                <input type="text" name="configuration_name[]" id="" placeholder="配置名" value="" class="input-text configuration_name[]" style=" width:25%">
                                <input type="text" name="configuration_information[]" id="" placeholder="信息" value="" class="input-text configuration_information[]" style=" width:25%">
                                <a href="javascript:;" onclick="add(this)">
                                    <p class="btn btn-success">＋</p>
                                </a>
                            </div>
                        </div>
                        <div class="con"></div>
                        <script>
                            function add(obj)
                            {
                                o = $(obj).parents('.product-color').clone();
                                o.find('label').html('');
                                o.find('input').val('');
                                o.find('a').html(' <p  class="btn btn-success">－</p>').attr('onclick','del(this)');
                                $('.con').before(o);
                            }
                            function del(obj){
                                $(obj).parents('.product-color').remove();
                            }
                        </script>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="btn_wrap">
        <div class="btn_wrap_pd">
            <input name="type" type="hidden" value="1">
            <button class="btn btn_submit mr10 " type="submit">提交</button>
<!--            <button class="btn btn_submit mr10 button" type="button">提交</button>-->
        </div>
    </div>
    </form>
</div>
<script type="text/javascript" src="{$config_siteurl}statics/js/common.js?v"></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/content_addtop.js"></script>
<script>
    $('.button').click(function () {
        var template_id = $('.template_id').val();
        var template_ids = $('.template_ids').val();
        var user_version = $('.user_version').val();
        var user_desc = $('.user_desc').val();
        var configuration_informations = [];
        $(".configuration_information").each(function(element) {
            var configuration_informations = $(this).val();
            }
        );
        console.log(configuration_informations);
    })
</script>
</body>
</html>
