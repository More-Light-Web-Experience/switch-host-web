/**
 * Created by saive on 17-1-3.
 * common js
 */
var host = window.location.protocol + '://' +  window.location.host;

    // 'findApi' : '/api/HostsItems.php?cate=find',
    // 'buildApi' : '/api/HostsItems.php?cate=build',
    // 'delApi' : '/api/HostsItems.php?cate=delete',
    // 'detailApi' : 'api/HostsItems.php?cate=detail',




$.post('/api/HostsItems.php?cate=find',function(callback){
    if(callback.code == 2000) {
        new Vue({
            el : '#HostDiv',
            data : {
                menus : callback.data,
                content : callback.data[0].detail,
                alias : 'system',
                isdisabled:true,
                addAlias:'',
            },
            methods : {
                findHost:function(alias,index) {    //查找host
                    var _self = this;
                    console.log(this.menus[index].alias);
                    if(_self.menus[index].alias == 'system') {
                        _self.isdisabled = true;
                    } else {
                        _self.isdisabled = false;
                    }

                    var length = _self.menus.length;
                    for(var i=0;i<length;i++) {
                        _self.menus[i].selected = false;
                    }
                    _self.menus[index].selected = true;

                    $.getJSON('/api/HostsItems.php?cate=detail&alias='+_self.menus[index].alias,function(callbackDetail){
                        _self.content = callbackDetail.data;
                        _self.alias = _self.menus[index].alias;
                    });
                },
                saveHost:function(content,alias) {  //保存host
                    var _self = this;
                    $.post('/api/HostsItems.php?cate=save',{content:content,alias:alias},function(CBsave){
                        if(CBsave.code == 2000) {
                            alert('保存成功！');
                        } else {
                            alert('保存失败！');
                        }
                    },'json');
                    console.log(content,alias);
                },
                useHost:function(alias) {   //应用host
                    console.log(alias);
                    if(alias == 'system') {
                        alert('抱歉系统文件不支持应用');
                    } else {
                        $.post('/api/HostsItems.php?cate=use',{alias:alias},function(CBDel){
                            if(CBDel.code == 2000) {
                                window.location.reload();
                            } else {
                                alert(CBDel.msg);
                            }
                        },'json');
                    }
                },
                delHost:function(alias) {   //删除host
                    if(alias == 'system') {
                        alert('抱歉系统文件不能删除！');
                    } else if(alias == 'common') {
                        alert('抱歉common是保护文件，您可以选择清空！');
                    } else {
                        if(confirm('确定要删除' + alias + '配置吗？')) {
                            $.post('/api/HostsItems.php?cate=del',{alias:alias},function(CBDel){
                                if(CBDel.code == 2000) {
                                    window.location.reload();
                                } else {
                                    alert(CBDel.msg);
                                }
                            },'json');
                        }
                    }
                },
                addHost:function() {
                    var _self = this;
                    var alias = _self.addAlias;
                    var length = _self.menus;
                    if(alias == '') {
                        alert('请输入配置文件名称！');
                        return ;
                    }
                    for(var i = 0;i<length;i++) {
                        if(_self.menus[i].alias == alias) {
                            alert('配置文件已经存在！');
                            i = length;
                            return ;
                        }
                    }

                    $.post('/api/HostsItems.php?cate=build',{alias:alias},function(CBBuild){
                        if(CBBuild.code == 2000) {
                            _self.menus.push(CBBuild.data);
                            _self.addAlias = '';
                        } else {
                            alert(CBBuild.msg);
                        }
                    },'json');
                }
            },
        });
    } else {
        return [];
    }
},'json');







