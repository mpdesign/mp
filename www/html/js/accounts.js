
//注册验证
function regverify()
{
	var user_name	= $("input[name='user_name']").val();
	var user_email	= $("input[name='user_email']").val();
	var user_pass	= $("input[name='user_pass']").val();
	if(user_name && user_email && user_pass)
	{
		return true;
	}
	else
	{
		alert('信息要填写完整');
		return false;
	}

}

//登录验证
function logverify()
{
	var user_email	= $("input[name='user_email']").val();
	var user_pass	= $("input[name='user_pass']").val();
	if(user_email && user_pass)
	{
		return true;
	}
	else
	{
		alert('填入邮箱或密码');
		return false;
	}
}
//注册绑定事件
function regprocess()
{
	$(function()
	{
		$("input[name='user_name']").mouseout(function(){
			if(!$(this).val())
			{
				$(this).parent().find('span').html('名号不能为空');
			}
			else
			{
				$(this).parent().find('span').html('');
			}
		});
		$("input[name='user_name']").click(function(){
			if(!$(this).val())
			{
				$(this).parent().find('span').html('名号不能没空');
			}
			else
			{
				$(this).parent().find('span').html('');
			}
		});

		$("input[name='user_email']").mouseout(function(){
			if(!$(this).val())
			{
				$(this).parent().find('span').html('邮箱不能为空');
			}
			else
			{
				if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test($(this).val()))
				{
					$(this).parent().find('span').html('邮箱格式不对');
				}
				else
				{	
					verify_email_have($(this).val(),$(this));
				}
			}
		});
		$("input[name='user_email']").click(function(){
			if(!$(this).val())
			{

				$(this).parent().find('span').html('邮箱不能为空');
			}
			else
			{
				if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test($(this).val()))
				{
					$(this).parent().find('span').html('邮箱格式不对');
				}
				else
				{
					$(this).parent().find('span').html('');
				}
			}
		});

		$("input[name='user_pass']").mouseout(function(){
			if(!$(this).val())
			{
				$(this).parent().find('span').html('密码不能为空');
			}
			else
			{
				$(this).parent().find('span').html('');
			}
		});
		$("input[name='user_pass']").click(function(){
			if(!$(this).val())
			{
				$(this).parent().find('span').html('密码不能为空');
			}
			else
			{
				$(this).parent().find('span').html('');
			}
		});
	});

}


//判断邮箱是否存在
function verify_email_have(email,obj)
{
	$.ajax({
		type	:'POST',
		url		:'/accounts/verify',
		data	:'op=email&email='+email,
		async	:false,
		success:function(msg)
		{
		}
	});
}
