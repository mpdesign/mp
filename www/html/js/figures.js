function initFigure(){
	img_list();
	checkbox();
	_submit();
	orderby();
	resizeimg();
}
function img_list(){
	var this_obj = $("body").find("#pager").find("a");
	this_obj.unbind('click').click(function(){
		
		var href = $(this).attr("href");
		
		$.ajax({
			url: href,
			type: 'POST',
			dataType: 'html',
			data: '',
			timeout: 2000,
			error: function(){
				$("#img_list").find("ul").html('网络加载失败，请重新加载！');
			},
			beforeSend: function(XMLHttpRequest) {
				$("#img_list").find("ul").html('正在加载数据...');
			},
			success: function(r){
				var _html = $(r).find("#img_list").html();
				$("#img_list").html(_html);
				initFigure();
			}
		});
		
		return false;
	});
}

function checkbox(){
	var this_obj = $("#img_list").find("#checkbox");
	this_obj.find("#check").unbind('click').click(function(){
		if($(this).attr("checked") == 'checked'){
			var figure_pid = $(this).parent().find("#pid").val();
			$(this).parent().find("#figure_pid").val(figure_pid);

			var figure_pic = $(this).parent().find("#pic").val();
			$(this).parent().find("#figure_pic").val(figure_pic);

			var figure_ext = $(this).parent().find("#ext").val();
			$(this).parent().find("#figure_ext").val(figure_ext);

			var figure_type = $(this).parent().find("#type").val();
			$(this).parent().find("#figure_type").val(figure_type);

			var figure_age = $(this).parent().find("#age").val();
			$(this).parent().find("#figure_age").val(figure_age);
		}else{
			$(this).parent().find("#figure_pid").val("");
			$(this).parent().find("#figure_pic").val("");
			$(this).parent().find("#figure_ext").val("");
			$(this).parent().find("#figure_type").val("");
			$(this).parent().find("#figure_age").val("");
		}
	});
}
//置顶排序
function orderby(){
	$("#order").click(function(){
		if($(this).attr("checked") == 'checked'){
			$(this).parent().find("#figure_order").val(999);
		}else{
			$(this).parent().find("#figure_order").val(0);
		}
	});
}

function _submit(){
	$("#figure_form").submit(function(){
		var url = $("#figure_form").attr("action");
		var ids = '';var pics = '';var types = '';var exts = '';var ages = '';
		$("input[name='figure_pid[]']").each(function(){
			if($.trim($(this).val()))
			ids += ','+$(this).val();
		});
		$("input[name='figure_pic[]']").each(function(){
			if($.trim($(this).val()))
			pics += ','+$(this).val();
		});
		$("input[name='figure_ext[]']").each(function(){
			if($.trim($(this).val()))
			exts += ','+$(this).val();
		});
		$("input[name='figure_type[]']").each(function(){
			if($.trim($(this).val()))
			types += ','+$(this).val();
		});
		$("input[name='figure_age[]']").each(function(){
			if($.trim($(this).val()))
			ages += ','+$(this).val();
		});
		var figure_uid = $("#figure_uid").val();
		var figure_order = $("#figure_order").val();
		
		if((!$.trim(ids) || ids == '') && !$.trim(figure_order)){
			$("#loading").html("请选择头像");return false;
		}
		$.ajax({
			url: url ,
			type: 'POST',
			dataType: 'json',
			data: 'ids='+ids+'&figure_uid='+figure_uid+'&pics='+pics+'&exts='+exts+'&types='+types+'&figure_order='+figure_order+'&ages='+ages,
			timeout: 3000,
			error: function(){
				$("#loading").html('网络加载失败，请重新提交');
			},
			beforeSend: function(XMLHttpRequest) {
				$("#loading").html('<img src="/images/loading.gif" />');
			},
			success: function(r){
				if(r.left < 0){
					$("#loading").html("操作失败！您之前已设置了"+r.exist+"个头像，本次操作多选择了"+Math.abs(r.left)+"个头像");
				}else{
					$("#loading").html("操作成功！您还有"+r.left+"个头像可以选择");
				}
				
			}
		});
		return false;
	});
}

function resizeimg(){
	$(".img").load().each(function(){
		SetImgWH($(this),110,110);
	});
	
}

//图片加载自适应
function SetImgWH(obj,maxW,maxH)
{
	var imgH=obj.height();
	var imgW=obj.width();

	if(obj.height()>maxH){
		obj.height(maxH);
		obj.width(imgW*(maxH/imgH));
		imgH=maxH;
		imgW=obj.width();
	}
   
	if(obj.width()>maxW){
		obj.width(maxW);
		obj.height((maxW/imgW)*imgH);
		imgW=maxW;
		imgH=obj.height();
	}
	obj.css("float","left");
	obj.css("margin-top",(maxH-obj.height())/2);
	obj.css("margin-left",(maxW-obj.width())/2);
}