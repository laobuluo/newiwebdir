//全选
function CheckAll(form){
	for (var i = 0; i < form.elements.length; i++) {
    	var e = form.elements[i];
        if (e.Name != "ChkAll" && e.disabled == false)
			e.checked = form.ChkAll.checked;
	}
}

//判断是否选择
function IsCheck(ObjName){
	var Obj = document.getElementsByName(ObjName); //获取复选框数组
    var ObjLen = Obj.length; //获取数组长度
    var Flag = false; //是否有选择
    for (var i = 0; i < ObjLen; i++) {
		if (Obj[i].checked == true) {
			Flag = true;
			break;
		}
	}
	return Flag;
}

//栏目合并判断
function ConfirmUnite() {
	if ($("#CurrentClassID").attr("value") == $("#TargetClassID").attr("value")) {
		alert("请不要在相同栏目内进行操作！");
		$("#TargetClassID").focus();
		return false;
	}
return true;
}

//获取META
function get_meta() {
	var $url = $.trim($('#web_url').val());
	if ($url == '') {
		$('#web_url').focus();
		return false;
	}
	$(document).ready(function(){$('#meta_btn').val('正在获取，请稍候...'); $.ajax({type: 'GET', url: '../?mod=ajaxget&type=metainfo&url=' + $url, datatype: 'json', cache: false, success: function($data){var $json = $.parseJSON($data); $('#web_title').val($json.title); $('#web_tags').val($json.keywords); $('#web_intro').val($json.description); $('#meta_btn').val('重新获取');}});});
}

//获取IP, PageRank, Sogou PageRank, Alexa
function get_wdata() {
	var $url = $.trim($('#web_url').val());
	if ($url == '') {
		$("#web_url").focus();
		return false;
	}
	$(document).ready(function(){$("#data_btn").val('正在获取，请稍候...'); $.ajax({type: 'GET', url: '../?mod=ajaxget&type=webdata&url=' + $url, datatype: 'json', cache: false, success: function($data){var $json = $.parseJSON($data); $('#web_ip').val($json.ip); $('#web_brank').val($json.brank); $('#web_grank').val($json.grank); $('#web_arank').val($json.arank); $('#data_btn').val('重新获取');}});});
}

//自动去除http
function strip_http() {
	var $url = $('#web_url').val();
    $url = $url.replace(' ', '');
	$('#web_url').val($url);
}

function strip_http() {
	var $url = $('#web_url').val();
    $url = $url.replace(' ', '');
	$('#web_url').val($url);
}