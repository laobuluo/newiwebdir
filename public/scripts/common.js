//搜索
$(document).ready(function(){
    $('#selopt').hover(
        function(){
            $('#options').slideDown();
            $('#options li a').click(function(){
                $('#cursel').text($(this).text());
                $('#type').attr('value', $(this).attr('name'));
                $('#options').hide();
            });
        },
        
        function(){$('#options').hide();}
    )   
})

//搜索伪静态
function rewrite_search(){
	var $type = $('#type').val();
	var $query = $.trim($('#query').val());
	if ($type == null) {$type = 'tags'}
	if ($query == '') {
		$('#query').focus();
		return false;
	} else {
		if ($linktype == 1) {
			window.location.href = $root + 'search-' + $type + '-' + encodeURI($query) + '.html';
		} else if ($linktype == 2) {
			window.location.href = $root + 'search/' + $type + '-' + encodeURI($query) + '-1';
		} else if ($linktype == 3) {
			window.location.href = $root + 'search/' + $type + '-' + encodeURI($query) + '.html';
		} else {
			this.form.submit();
		}
	}
	return false;
}

//评论
function post_comment() {
	var $content = $('#content').val();
	var $email = $('#email').val();
	var $nick = $('#nick').val();
	var $wid = parseInt($('#wid').val());
	var $rid = parseInt($('#rid').val());
	if ($content == '') {
		$('#content').focus();
		return false;
	} else {
		if ($content.length > 250) {
			alert('内容长度超过250个字符！');	
			return false;
		}
	}
	if ($email == '') {
		$('#email').focus();
		return false;
	} else {
		var $reg = /^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/;	
		if (!$reg.test($email)) {
			alert('Email格式不正确！');
			$('#email').focus();
			return false;
		}
	}
	if ($nick == '') {
		$('#nick').focus();
		return false;
	}
	
	$.ajax({
		type: 'POST',
		url: $root + '?mod=ajaxpost',
		datatype: 'html',
		data: {'type' : 'comment', 'rid' : $rid, 'wid' : $wid, 'content' : $content, 'email' : $email, 'nick' : $nick},
		success: function($data) {
			if ($data == 1) {
				location.reload();
			} else {
				alert($data);	
			}
		}	
	});
	return false;
}

//自动去除http
function strip_http() {
	var $url = $('#url').val();
    
	$url = $url.replace(' ', '');
	$('#url').val($url);
}

//获取META
function get_meta() {
	var $url = $.trim($('#url').val());
	if ($url == '') {
		$('#url').focus();
		return false;
	}
	$(document).ready(function(){$('#meta_btn').val('正在获取，请稍候...'); $.ajax({type: 'GET', url: $root + '?mod=ajaxget&type=metainfo&url=' + $url, datatype: 'json', cache: false, success: function($data){var $json = $.parseJSON($data); $('#title').val($json.title); $('#tags').val($json.keywords); $('#intro').val($json.description); $('#meta_btn').val('重新获取');}});});
}

//验证url
function checkurl($url){
	if ($url == '') {
		$("#msg").html('请输入网站域名！');
		return false;
	}
	
	$(document).ready(function(){$('#msg').html('<img src="' + $root + 'public/images/loading.gif" align="absmiddle"> 正在验证，请稍候...'); $.ajax({type: 'GET', url: $root + '?mod=ajaxget&type=check&url=' + $url, cache: false, success: function($data){if ($data == 1) {$('#msg').html('<input type="button" class="fbtn" id="meta_btn" value="抓取Meta" onclick="get_meta();">')} else {$('#msg').html('<font color="#ff0000">' + $data + '</font>');}}});});
return true;
};

//点出统计
function clickout($wid) {
	$(document).ready(function(){$.ajax({type: 'GET', url: $root + '?mod=getdata&type=outstat&wid=' + $wid, cache: false, success: function(data){}});});
};

//错误报告
function report($obj, $wid) {
	$(document).ready(function(){if (confirm('确认报告此错误吗？')){ $('#' + $obj).html('正在提交，请稍候...'); $.ajax({type: 'GET', url: $root + '?mod=getdata&type=error&wid=' + $wid, cache: false, success: function($data){$('#' + $obj).html($data);}})};});
};

//刷新验证码
function refreshimg($obj) {
	var $randnum = Math.random();
	$('#' + $obj).html('<img src="' + $root + 'source/include/captcha.php?s=' + $randnum + '" align="absmiddle" alt="看不清楚?换一张" onclick="this.src+='+ $randnum +'" style="cursor: pointer;">');
}