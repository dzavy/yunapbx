<?php
function smarty_function_dhtml_calendar($params, &$smarty) {
	require_once (dirname(__FILE__).'/../../jscalendar/calendar.php');

	$jscalendar_options['firstDate']  = 1;
	$jscalendar_options['showsTime']  = false;
	$jscalendar_options['timeFormat'] = "24";
	$jscalendar_options['showOthers'] = false;
	$jscalendar_options['ifFormat']   = "%Y/%m/%d";

	$jscalendar_properties['style'] = '';
	$jscalendar_properties['name']  = '';
	$jscalendar_properties['value'] = '';


    if (empty($params['name'])) {
        $smarty->trigger_error("dhtml_calendar: missing 'name' parameter");
        return;
    } else {
        $jscalendar_properties['name'] = $params['name'];
    }

    if (!empty($params['format'])) {
    	$jscalendar_options['ifFormat'] = $params['format'];
    }

    if (!empty($params['value'])) {
    	$jscalendar_properties['value'] = $params['value'];
    }

    if (!empty($params['style'])) {
		$jscalendar_properties['style'] = $params['style'];
    }
	if (!empty($params['class'])) {
		$jscalendar_properties['class'] = $params['class'];
	}
    if (strstr($jscalendar_options['ifFormat'], '%H')) {
    	$jscalendar_options['showsTime']  = true;
    }

	$calendar = new DHTML_Calendar(dirname(__FILE__).'/../../jscalendar/');
	return $calendar->make_input_field($jscalendar_options, $jscalendar_properties);
}
?>
