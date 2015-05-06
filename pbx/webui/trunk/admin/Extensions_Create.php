<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_Create() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init default template (FK_Template)
    $query = "SELECT PK_Template FROM Templates WHERE Protected = 1 LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    $row = $result->fetch_array();
    $FK_Template = $row[0];

    // If user chosed Type and FK_Template
    if ($_REQUEST['submit'] == 'create') {
        $Type = $_REQUEST['Type'];
        $FK_Template = $_REQUEST['FK_Template'];
        $Feature = $_REQUEST['Feature'];

        if ($Type != 'Feature') {
            $location = form_location($Type, $FK_Template);
        } else {
            $location = form_location($Feature, '');
        }
        if ($location != "") {
            return "
				<script>
					document.location='$location'
				</script>
			";
        } else {
            $Errors['Type'] = true;
        }
    }

    // Available extension tempaltes (Templates)
    $query = "
		SELECT
			PK_Template,
			Name
		FROM
			Templates
		ORDER BY
			Name
	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);

    $Templates = array();
    while ($row = $result->fetch_assoc()) {
        $Templates[] = $row;
    }

    $smarty->assign('Templates', $Templates);
    $smarty->assign('FK_Template', $FK_Template);
    $smarty->assign('Feature', $Feature);
    $smarty->assign('Type', $Type);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('Extensions_Create.tpl');
}

function form_location($Type, $FK_Template) {
    $location = "Extensions_{$Type}_Modify.php?FK_Template=$FK_Template";

    return $location;
}

admin_run('Extensions_Create', 'Admin.tpl');
?>