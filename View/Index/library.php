<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="table_list">
  <table width="100%" cellspacing="0" >
      <thead>
      <a style="margin: 10px 0 10px 0;" href="{:U('index')}" class="btn btn-success">返回列表</a>
        <tr>
          <td width="80">版本号</td>
          <td width="80">描述</td>
          <td width="80">来源小程序</td>
          <td>TemplateID</td>
          <td width="80">小程序AppID</td>
          <td width="80">最近上传开发者</td>
          <td>最近提交时间</td>
          <td align='center'>管理操作</td>
        </tr>
      </thead>
      <tbody>
        <volist name="template_list" id="r">
        <tr>
          <td>{$r.user_version}</td>
          <td>{$r.user_desc}</td>
          <td>{$r.source_miniprogram}</td>
          <td>{$r.template_id}</td>
          <td>{$r.source_miniprogram_appid}</td>
          <td>{$r.developer}</td>
          <td>{$r.create_time}</td>
          <td align='center'>
          <?php
		  $op = array();
          $op[] = '<a href="'.U('index/delect_library',array('template_id'=>$r['template_id'])).'">从模板库中删除</a>';
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
