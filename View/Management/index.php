 
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="table_list">
  <table width="100%" cellspacing="0" >
      <thead>
        <tr>
          <td width="80">ID</td>
          <td width="80">第三方id</td>
          <td width="80">第三方程序名称</td>
          <td width="80">第三方程序介绍</td>
          <td>第三方二维码</td>
          <td align='center'>管理操作</td>
        </tr>
      </thead>
      <tbody>
        <volist name="info" id="r">
        <tr>
          <td>{$r.id}</td>
          <td style="width: 300px;">{$r.authorizer_appid}</td>
          <td style="width: 300px;">{$r.authorizer_name}</td>
          <td style="width: 400px;">{$r.authorizer_signature}</td>
          <td style=" text-align:center; width: 300px;"><img style="width: 250px; height: 250px;" src="{$r.authorizer_qrcode_url}"></td>
          <td align='center'>
          <?php
		  $op = array();
          $op[] = '<a href="'.U('Management/management',array('authorizer_appid'=>$r['authorizer_appid'])).'">管理</a>';
		  if(\Libs\System\RBAC::authenticate('delete')){
			  $op[] = '<a class="J_ajax_del" href="'.U('Urlrule/delete',array('urlruleid'=>$r['urlruleid'])).'">删除</a>';
		  }
		  echo implode(" | ",$op);
		  ?>
          </td>
        </tr>
        </volist>
      </tbody>
    </table>
  </div>
</div>
<script src="{$config_siteurl}statics/js/common.js"></script>
</body>
</html>
