<?php
/**
* concat
*
* Easier way to concat variables in templates.
*
* Programmed by: Joe Li <licp@hotmail.com>
* Website: http://www.geocities.com/cpli_hk/
*
* Example: {concat var="abc" value="append"}
*/
function smarty_function_concat($params, &$smarty)
{
	$var_name = '';
	$var_value = '';
	foreach($params as $key => $value) {
		switch (strtolower($key)) {
			case 'var': $var_name = $value; break;
			case 'value': $var_value = $value; break;
			default: break;
		}
	}
	if (empty($var_name)) {
		return;
	} else {
		$cur = $smarty->get_template_vars($var_name);
		$smarty->assign($var_name, empty($cur) ? $value : $cur. $value);
	}
}
?>
