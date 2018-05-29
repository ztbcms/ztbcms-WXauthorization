 
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
      <li class="current"><a href="javascript:;;">基本属性</a></li>
    </ul>
  </div>
  <form class="J_ajaxForms" name="myform" id="myform" action="{:U("index")}" method="post">
    <div class="J_tabs_contents">
      <div>
        <div class="h_a">微信必备基本参数</div>
        <div class="table_full">
          <table width="100%" class="table_form ">
            <tr>
              <th>第三方平台AppID（必填）：</th>
              <td><input type="text" name="trilateralAppID" id="catname" class="input" value="{$data.trilateralappid}"></td>
            </tr>
            <tr>
              <th>第三方平台AppSecret（必填）：</th>
              <td><textarea name="trilateralAppSecret" maxlength="255" style="width:300px;height:60px;">{$data.trilateralappsecret}</textarea></td>
            </tr>
              <tr>
                  <th>消息校验Token（必填）：</th>
                  <td><input type="text" name="trilateralToken" id="seturl" class="input length_6" value="{$data.trilateraltoken}"><span class="gray"> 可随意设置但是必须与微信第三方平台填写的一致</span></td>
              </tr>
            <tr>
              <th>消息加解密Key(必填)：</th>
              <td><input type="text" name="trilateralKey" id="seturl" class="input length_6" value="{$data.trilateralkey}"><span class="gray"> 可随意设置但是必须与微信第三方平台填写的一致</span></td>
            </tr>
            <tr>
              <th>微信第三方平台名称：</th>
              <td><input type="text" name="trilateralName" id="seturl" class="input length_6" value="{$data.trilateralname}"><span class="gray"> 用于分辨第三方平台 </span></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="btn_wrap">
      <div class="btn_wrap_pd">
        <input name="type" type="hidden" value="1">
        <button class="btn btn_submit mr10 " type="submit">提交</button>
          <a href="{:U('index/get_component_access_token')}" class="btn btn-success">点击验证第三方授权授权</a>
          <if condition="($data.trilateralurl neq null)">
              <a href="{$data.trilateralurl}" class="btn btn-success">进入授权页面</a>
          </if>
          <a href="{:U('index/draft')}" class="btn btn-success">小程序的草稿箱</a>
          <a href="{:U('index/library')}" class="btn btn-success">小程序的模板库</a>
      </div>
    </div>
  </form>
</div>
<script type="text/javascript" src="{$config_siteurl}statics/js/common.js?v"></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/content_addtop.js"></script>

</body>
</html>
