<?php

return array(
    array(
        //父菜单ID，NULL或者不写系统默认，0为顶级菜单
        "parentid" => 37,
        //地址，[模块/]控制器/方法
        "route" => "WXauthorization/Index/index",
        //类型，1：权限认证+菜单，0：只作为菜单
        "type" => 0,
        //状态，1是显示，0不显示（需要参数的，建议不显示，例如编辑,删除等操作）
        "status" => 1,
        //名称
        "name" => "微信第三方授权",
        //备注
        "remark" => "进行微信第三方授权",
        //子菜单列表
        "child" => array(
             array(
                 "route" => "WXauthorization/Index/index",
                 "type" => 1,
                 "status" => 1,
                 "name" => "微信基本参数列表",
             ),
             array(
                 "route" => "WXauthorization/Management/index",
                 "type" => 1,
                 "status" => 1,
                 "name" => "第三授权管理",
             ),
        ),
    ),
);
