
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
            <li class="current"><a href="javascript:;;">提交审核的参数</a></li>
        </ul>
    </div>
    <form class="J_ajaxForms" name="myform" id="myform" action="{:U('submit_audit')}" method="post">
    <div class="J_tabs_contents">
        <div>
            <div class="h_a">提交审核的参数</div>
            <div class="table_full">
                <table width="100%" class="table_form ">
                    <tr>
                        <th>可选类目</th>
                        <td>
                            <volist name="category_list" id="foo">
                                <p>第一类目ID编号：{$foo.first_id}；第一类目名称：{$foo.first_class}</p>
                                <p>第二类目ID编号：{$foo.second_id}；第二类目名称：{$foo.second_class}</p>
                                <p>第三类目ID编号：{$foo.third_id};第三类目名称：{$foo.third_class}</p>
                            </volist>
                        </td>
                    </tr>
                    <tr>
                        <th>可选目录</th>
                        <td>
                            <volist name="page_list" id="foo">
                                <p>{$foo}</p>
                            </volist>
                        </td>
                    </tr>
                    <input type="hidden" value="{$access_token}" name="access_token">
                    <tr>
                        <th>提交审核项的一个列表（至少填写1项，至多填写5项）（ 两行选项有一个空未填直接取消整行信息）：</th>
                        <td>
                        <div style="margin-left: 62px;"  class="row cl product-color">
                            <div style="margin-top: 10px;" class="formControls col-xs-8 col-sm-9">
                                <input type="text" name="address[]" id="" placeholder="小程序的页面" value="" class="input-text configuration_name[]" style=" width:25%">
                                <input type="text" name="tag[]" id="" placeholder="小标签" value="" class="input-text configuration_information[]" style=" width:25%">
                                <input type="text" name="first_class[]" id="" placeholder="一级类目名称" value="" class="input-text configuration_information[]" style=" width:25%">
                                <input type="text" name="second_class[]" id="" placeholder="二级类目" value="" class="input-text configuration_information[]" style=" width:25%">
                                <input type="text" name="third_class[]" id="" placeholder="三级类目" value="" class="input-text configuration_information[]" style=" width:25%">
                                <input type="text" name="first_id[]" id="" placeholder="一级类目的ID" value="" class="input-text configuration_information[]" style=" width:25%">
                                <input type="text" name="second_id[]" id="" placeholder="二级类目的ID" value="" class="input-text configuration_information[]" style=" width:25%">
                                <input type="text" name="third_id[]" id="" placeholder="三级类目的ID" value="" class="input-text configuration_information[]" style=" width:25%">
                                <input type="text" name="title[]" id="" placeholder="小程序页面的标题,标题长度不超过32" value="" class="input-text configuration_information[]" style=" width:25%">
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
        </div>
    </div>
    </form>
</div>
<script type="text/javascript" src="{$config_siteurl}statics/js/common.js?v"></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/content_addtop.js"></script>
</body>
</html>
