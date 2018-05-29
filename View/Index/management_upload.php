 
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
  <form class="J_ajaxForms" name="myform" id="myform" action="{:U("ProgramApi/wx_upload_code",array('authorizer_appid'=> $authorizer_appid))}" method="post">
    <div class="J_tabs_contents">
      <div>
        <div class="h_a">代码上传必备参数</div>
        <div class="table_full">
          <table width="100%" class="table_form ">
            <tr>
              <th>代码库中的代码模版ID(template_id)（必填）：</th>
              <td><input type="text" name="template_id" id="catname" class="input" value="{$data.trilateralappid}"></td>
            </tr>
            <tr>
              <th>第三方自定义的配置(ext_json)（必填）：</th>
              <td><textarea name="ext_json" maxlength="255" style="width:300px;height:60px;">{$data.trilateralappsecret}</textarea></td>
            </tr>
              <tr>
                  <th>代码版本号，开发者可自定义(user_version)（必填）：</th>
                  <td><input type="text" name="user_version" id="seturl" class="input length_6" value="{$data.trilateraltoken}"></td>
              </tr>
            <tr>
              <th>代码描述，开发者可自定义(user_desc) （必填）：</th>
              <td><input type="text" name="user_desc" id="seturl" class="input length_6" value="{$data.trilateralkey}"></td>
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
