<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty cat modifier plugin
 *
 * Type:     modifier<br>
 * Name:     cat<br>
 * Date:     Feb 24, 2003
 * Purpose:  catenate a value to a variable
 * Input:    string to catenate
 * Example:  {$var|cat:"foo"}
 * @link http://smarty.php.net/manual/en/language.modifier.cat.php cat
 *          (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @version 1.0
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_substr_cn($string, $start = 0, $length = 80, $code = 'UTF-8')
{
	$string = preg_replace("/(<\/?)(\w+)([^>]*>)/","",$string);
    if ($length == 0)

        return '';

    if ($code == 'UTF-8') {

        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";

    }

    else {

    $pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";

    }

    preg_match_all($pa, $string, $t_string);

    if (count($t_string[0]) > $length)

        return join('', array_slice($t_string[0], $start, $length)) ;

    return join('', array_slice($t_string[0], $start, $length)) ;
}



?>
