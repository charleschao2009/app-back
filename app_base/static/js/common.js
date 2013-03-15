
/*
ajax get获取,使用方法：
ajax_get('2.php',function(data){
	alert(data);
});
*/
function ajax_get(url,callback){
	var xmlhttp;
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			callback(xmlhttp.responseText);
		}
	}
	xmlhttp.open("GET",url,true);
	xmlhttp.send();
}


/*
js处理地址栏参数，使用方法：
var Request=new UrlSearch(); //实例化
alert(Request.sId);//调用
*/
function UrlSearch() {
	var name,value;  
	var str=location.href; //取得整个地址栏
	var num=str.indexOf("?")  
	str=str.substr(num+1); //取得所有参数
	var arr=str.split("&"); //各个参数放到数组里
	for(var i=0;i < arr.length;i++){
		num=arr[i].indexOf("=");  
		if(num>0){
			name=arr[i].substring(0,num);
			value=arr[i].substr(num+1);
			this[name]=value;
		}  
	}
}



//URL 编码,utf-8实现
//同return encodeURIComponent(string);
//查看文章：http://www.ruanyifeng.com/blog/2010/02/url_encoding.html
function urlEncode(string) {
	string = string.replace(/\r\n/g,"\n");
	var utftext = "";  
	for (var n = 0; n < string.length; n++) {  
		var c = string.charCodeAt(n);  
		if (c < 128) {
			utftext += String.fromCharCode(c);
		}
		else if((c > 127) && (c < 2048)) {
			utftext += String.fromCharCode((c >> 6) | 192);
			utftext += String.fromCharCode((c & 63) | 128);
		}
		else {
			utftext += String.fromCharCode((c >> 12) | 224);
			utftext += String.fromCharCode(((c >> 6) & 63) | 128);
			utftext += String.fromCharCode((c & 63) | 128);
		}
	}	
	utftext=utftext.replace('+','%2B');
	//utftext=utftext.replace(' ','%20');
	return escape(utftext);
}

function urlDecode(utftext) {
	utftext=unescape(utftext);
	var string = "";
	var i = 0;
	var c = c1 = c2 = 0;  
	while ( i < utftext.length ) {  
		c = utftext.charCodeAt(i);  
		if (c < 128) {
			string += String.fromCharCode(c);
			i++;
		}
		else if((c > 191) && (c < 224)) {
			c2 = utftext.charCodeAt(i+1);
			string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			i += 2;
		}
		else {
			c2 = utftext.charCodeAt(i+1);
			c3 = utftext.charCodeAt(i+2);
			string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}  
	}
	string=string.replace('%2B','+');
	return string;
 }



//others
function sleep(seconds){
	var d1 = new Date();
	var t1 = d1.getTime();
	for (;;){
		var d2 = new Date();
		var t2 = d2.getTime();
		if (t2-t1 > seconds*1000){
			break;
		}
	}
}

/***打印出 json数据*/
function pr(theObj) {
	var retStr = '';
	if (typeof theObj == 'object') {
		retStr += '<div style=\'font-size:13px;border-left:1px solid #eee;padding-left:3px;background:#f6f6f6;color:#666;font-family: "Consolas","Courier New"\'>';
		for (var p in theObj) {
			if (typeof theObj[p] == 'object') {
				retStr += '<div><b>['+p+'] => ' + typeof(theObj) + '</b></div>';
				retStr += '<div style="padding-left:25px;">' + pr(theObj[p]) + '</div>';
			} else {
				retStr += '<div>['+p+'] => <b>' + theObj[p] + '</b></div>';
			}
		}
		retStr += '</div>';
	}
	return retStr;
}


//字符串转换为对象 ，json字符串
function str2obj(json){
   return eval("("+json+")");
}

//对象转换为字符串，json字符串。
function obj2str(o){
   var r = [];
   if(typeof o == "string" || o == null) {
     return o;
   }
   if(typeof o == "object"){
     if(!o.sort){
       r[0]="{"
       for(var i in o){
         r[r.length]=i;
         r[r.length]=":";
         r[r.length]=obj2str(o[i]);
         r[r.length]=",";
       }
       r[r.length-1]="}"
     }else{
       r[0]="["
       for(var i =0;i<o.length;i++){
         r[r.length]=obj2str(o[i]);
         r[r.length]=",";
       }
       r[r.length-1]="]"
     }
     return r.join("");
   }
   return o.toString();
}

//是否在数组中。
function  inArray(arr,value) {
    for (var i=0,l = arr.length ; i <l ; i++) {
        if (arr[i] === value) {
            return true;
        }
    }
    return false;
}