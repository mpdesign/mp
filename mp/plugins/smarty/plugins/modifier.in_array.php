<?php

function smarty_modifier_in_array($needle = '', $array = array())
{
	if(!is_array($array) || !$needle)return false;
	if(in_array($needle, $array)){
		
		return true;
	}else{
		return false;
	}
}



?>
