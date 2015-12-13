<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Templates_List() {
    global $mysqli;
    
    $session = &$_SESSION['Templates'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // If a create template was requested
    if ($_REQUEST['submit'] == 'create') {
        $PK_Template = create_new_template($_REQUEST['Name']);

        if ($PK_Template) {
            header("Location: Templates_Modify.php?PK_Template=$PK_Template&msg=CREATE_TEMPLATE");
            die();
        } else {
            $Message = "ERR_TEMPLATE_NAME";
        }
    }

    // Init no element on page (PageSize)
    $PageSize = 50;

    // Init sort order (Order)
    if ($session['Sort'] == $_REQUEST['Sort']) {
        $Order = ($session['Order'] == "asc" ? "desc" : "asc");
    } elseif ($session['Sort'] != $_REQUEST['Sort']) {
        $Order = 'asc';
    }
    $session['Order'] = $Order;

    // Init sort field (Sort)
    if (isset($_REQUEST['Sort'])) {
        $Sort = $_REQUEST['Sort'];
    } else {
        $Sort = 'Name';
    }
    $session['Sort'] = $Sort;

    // Init listing start (Start)
    if (isset($_REQUEST['Start'])) {
        $Start = $_REQUEST['Start'];
    } else {
        $Start = 0;
    }

    // Init total entries (Total)
    $query = "SELECT COUNT(PK_Template) FROM Templates;";
    $result = $mysqli->query($query) or die($mysqli->error);
    $row = $result->fetch_array();
    $Total = $row[0];

    // Init table fields (Templates)
    $Templates = array();
    $query = "
		SELECT
			PK_Template AS _PK_,
			Name                 AS Name,
			DATE_FORMAT(DateCreated,'%m/%d/%y, %h:%i %p') AS DateCreated,
			Protected
		FROM
			Templates
		ORDER BY 
			$Sort $Order
		LIMIT $Start, $PageSize

	";
    $result = $mysqli->query($query) or die($mysqli->error);
    while ($row = $result->fetch_assoc()) {
        $Templates[] = $row;
    }

    // Init end record (End)
    $End = $Start + count($Templates);

    $smarty->assign('Templates', $Templates);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Start', $Start);
    $smarty->assign('End', $End);
    $smarty->assign('Total', $Total);
    $smarty->assign('PageSize', $PageSize);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('Templates_List.tpl');
}

function create_new_template($Name) {
    if ($Name == "") {
        return false;
    }

    $query = "INSERT INTO Templates() VALUES()";
    $mysqli->query($query) or die($mysqli->error);
    $PK_Template = $mysqli->insert_id;

    $query = "
		UPDATE
			Templates
		SET
			Name               = '" . $mysqli->real_escape_string($Name) . "',
			FirstName_Editable = 1,
			LastName_Editable  = 1,
			Password_Editable  = 1,
			Email_Editable     = 1,
			FK_NATType         = 1,
			FK_DTMFMode        = 1
		WHERE
			PK_Template = $PK_Template
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error);

    $query = "INSERT INTO Template_Codecs (FK_Template, FK_Codec) (SELECT $PK_Template, PK_Codec FROM Codecs WHERE Recomended = 1)";
    $mysqli->query($query) or die($mysqli->error . $query);

    $query = "INSERT INTO Template_Features (FK_Template, FK_Feature) (SELECT $PK_Template, PK_Feature FROM Features WHERE Recomended = 1)";
    $mysqli->query($query) or die($mysqli->error . $query);

    return $PK_Template;
}

admin_run('Templates_List', 'Admin.tpl');
?>