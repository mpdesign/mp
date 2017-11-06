<?php

function smarty_modifier_strtotime($string = '')
{
	if(!$string)return false;
	return strtotime($string);
}



?>
