/*
* iframe之间事件操作api接口  每个frame需要name和id，作为兄弟名参数，以便兼容多浏览器
* 需要在页面加入
<input  type='hidden' action="" value="" id='frameApi' onclick='frameApi()'/>。作为传递。
* 调用例子：调用main下面的goUrl函数，
* frameFun('main','goUrl','"'+url+'"');
* 参数为字符串的需要加引号
* 
*/

//其他窗口调用该窗口函数，
//方法：修改id=frameMainApi 的action【函数】和value【参数】
function frameApi(){
	var action=$("#frameApi").attr('action');
	var value=$("#frameApi").attr('value');
	var fun=action+'('+value+');';//拼装执行语句，字符串转换到代码
	eval(fun);
}

//该窗口调用其他窗口的api
//调用iframe框架的js函数.封装控制器。
function frameFun(iframe,action,value){
	var ie = !-[1,];//是否ie

	if (ie){//获取兄弟frame的dom树
		var obj=window.parent.document.getElementById(iframe).contentDocument;//IE
	}else{
		var obj=window.parent.frames[iframe].document;//chrome safari firefox...
	}
	obj=obj.getElementById("frameApi");
	$(obj).attr("action",action);
	$(obj).attr("value",value);
	obj.click();	
}

function goUrl(url){
	window.location.href=url;
}