<?php
/*
* Smarty plugin
*
-------------------------------------------------------------
* Purpose: assume the input string is a number, increment/decrement and return result
* Install: Drop into the plugin directory.
* Params: mixed number the number to alter, needed
* mixed alteration the alteration to use, default is 1
*
* Usage: {$myvar|add} increments $myvar by one
* {$myvar|add:-1} decrements $myvar by one
* {$myvar|add:3} increments $myvar by 3
*
-------------------------------------------------------------
*/
function smarty_modifier_add($number, $alteration = 1)
{
return $number += $alteration;
}
?>