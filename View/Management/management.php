<extend name="../../Admin/View/Common/base_layout"/>
<block name="content">

    <div id="app" style="padding: 8px;display: none;">
        <h4>操作列表</h4>
        <table width="100%" cellspacing="0" >
            <a style="margin: 10px 0 10px 0; border-color: #00a0e9;background-color:#00a0e9; color: #3d3d3d;" href="{:U('index')}" class="btn ">返回列表</a>
            <a style="margin:20px 20px 20px 20px;" href="{:U('Management/authorizer_refresh_token',array('authorizer_appid'=> $authorizer_appid ))}" class="btn btn-success">刷新令牌</a>
            <a style="margin-bottom: 20px; margin-top: 20px;" href="{:U('Management/wx_upload_code', array('authorizer_appid'=> $authorizer_appid ))}" class="btn btn-success">上传小程序代码</a>
            <a style="margin:20px 20px 20px 20px;" href="{:U('Management/getExpCode', array('authorizer_appid'=> $authorizer_appid ))}" class="btn btn-success">下载小程序的体验二维码</a>
            <a style="margin:20px 20px 20px 20px;" href="{:U('submit_audit',array('authorizer_appid'=> $authorizer_appid ))}" class="btn btn-success">第三方提交的代码包提交审核</a>
            <a style="margin:20px 20px 20px 20px;"  class="btn btn-success query_auditid">查询某个指定版本的审核状态</a>
            <a style="margin:20px 20px 20px 20px;"  class="btn btn-success post_latest">查询最新一次提交的审核状态</a>
            <a style="margin:20px 20px 20px 20px;"  class="btn btn-success release_program">发布已通过审核的小程序</a>
            <a style="margin:20px 20px 20px 20px;"  class="btn btn-success versionBack">小程序版本回退</a>
            <input type="hidden" class="authorizer_appid" value="{$authorizer_appid}">
        </table>
        <hr>
        <div class="search_type cc mb10">
            发布时间：
            <input type="text" name="start_date" class="input datepicker" >
            -
            <input type="text" name="end_date" class="input datepicker">
            <button class="btn btn-primary" style="margin-left: 8px;" @click="search">搜索</button>
        </div>
        <hr>
        <div class="table_list">
            <table class="table table-bordered table-hover">
                <thead>
                <tr style="background: gainsboro;">
                    <td align="center" width="80">ID</td>
                    <td align="center">authorizer_appid</td>
                    <td align="center" width="300">模板号\审核编号</td>
                    <td align="center">内容</td>
                    <td align="center" width="160">发布时间</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in logs">
                    <td align="center">{{item.id}}</td>
                    <td align="center">{{item.authorizer_appid}}</td>
                    <td align="center">{{item.auditid}}</td>
                    <td align="center"><div style="word-wrap:break-word">{{item.status}}</div></td>
                    <td align="center">{{item.addtime|getFormatTime}}</td>
                </tr>
                </tbody>
            </table>

            <div style="text-align: center">
                <ul class="pagination pagination-sm no-margin">
                    <button @click="toPage( parseInt(where.page) - 1 )" class="btn btn-primary">上一页</button>
                    {{ where.page }} / {{ total_page }}
                    <button @click="toPage( parseInt(where.page) + 1 )" class="btn btn-primary">下一页</button>
                    <span style="line-height: 30px;margin-left: 10px;"><input id="ipt_page"
                                                                              style="width:50px;text-align: center;"
                                                                              type="text" v-model="temp_page"></span>
                    <span><button class="btn btn-primary" @click="toPage( temp_page )">跳转</button></span>
                </ul>
            </div>
        </div>
    </div>

    <script>
        $(document).on('click','.post_latest',function(){
            var authorizer_appid = $('.authorizer_appid').val();
            $.post("{:U('Management/get_latest')}",{authorizer_appid:authorizer_appid},function (res) {
                var data = JSON.parse(res);
                if(data.errcode == 0){
                    var bt = "审核编号为"+data.auditid+"，审核状态为"+data.status;
                    layer.alert(bt);
                }
            })
        })

        $(document).on('click','.release_program',function(){
            var authorizer_appid = $('.authorizer_appid').val();
            $.post("{:U('Management/release_program')}",{authorizer_appid:authorizer_appid},function (res) {
                var data = JSON.parse(res);
                layer.alert(data.errmsg);
            })
        })

        $(document).on('click','.query_auditid',function(){
            var authorizer_appid = $('.authorizer_appid').val();
            layer.prompt({title: '输入审核编号', formType: 3}, function(text, index){
                $.post("{:U('Management/query_auditid')}",{authorizer_appid:authorizer_appid,text:text},function (res) {
                    var data = JSON.parse(res);
                    if(data.errcode == 0){
                        var bt = "审核状态为"+data.status;
                        layer.alert(bt);
                    }
                })
            });
        })
        
        $(document).on('click','.versionBack',function () {
            var authorizer_appid = $('.authorizer_appid').val();
            $.post("{:U('Management/versionBack')}",{authorizer_appid:authorizer_appid},function (res) {
                var data = JSON.parse(res);
                layer.alert(data.errmsg);
            })
        })
    </script>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    where: {
                        category: '',
                        message: '',
                        start_date: '',
                        end_date: '',
                        page: 1,
                        limit: 20,
                    },
                    logs: {},
                    temp_page: 1,
                    total_page: 0
                },
                filters: {
                    getFormatTime: function (value) {
                        var time = new Date(parseInt(value * 1000));
                        var y = time.getFullYear();
                        var m = time.getMonth() + 1;
                        var d = time.getDate();
                        var h = time.getHours();
                        var i = time.getMinutes();
                        var res = y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d) + '';
                        res += '  ' + (h < 10 ? '0' + h : h) + ':' + (i < 10 ? '0' + i : i);
                        return res;
                    }
                },
                methods: {
                    getList: function () {
                        var that = this;
                        $.ajax({
                            url: '{:U("WXauthorization/VueList/content_list")}',
                            data: that.where,
                            type: 'get',
                            dataType: 'json',
                            success: function (res) {
                                if (res.status) {
                                    that.logs = res.data.items;
                                    that.where.page = res.data.page;
                                    that.where.limit = res.data.limit;
                                    that.temp_page = res.data.page;
                                    that.total_page = res.data.total_page;
                                }
                            }
                        });
                    },
                    toPage: function (page) {
                        this.where.page = page;
                        if (this.where.page < 1) {
                            this.where.page = 1;
                        }
                        if (this.where.page > this.total_page) {
                            this.where.page = this.total_page;
                        }
                        this.getList();
                    },
                    search: function () {
                        this.where.page = 1;
                        this.where.start_date = $('input[name="start_date"]').val();
                        this.where.end_date = $('input[name="end_date"]').val();
                        this.getList();
                    }
                },
                mounted: function () {
                    document.getElementById('app').style.display = 'block';
                    this.getList();
                }
            });
        });
    </script>

</block>
