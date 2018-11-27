<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function SoundEntries_List() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['SoundEntries'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

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

    if (isset($_REQUEST['PK_SoundFolder'])) {
        $PK_SoundFolder = $_REQUEST['PK_SoundFolder'];
    } elseif ($PK_SoundFolder == '') {
        $PK_SoundFolder = $session['PK_SoundFolder'];
    }
    $session['PK_SoundFolder'] = $PK_SoundFolder;

    // Init total entries (Total)
    $query = "SELECT COUNT(PK_SoundEntry) FROM SoundEntries " . (empty($PK_SoundFolder) ? '' : "WHERE FK_SoundFolder = {$PK_SoundFolder}");
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch_array();
    $Total = $row[0];

    // Init Folders (SoundFolders)
    $SoundFolders = array();
    $query = "
		SELECT
			PK_SoundFolder       AS _PK_,
			PK_SoundFolder             ,
			COUNT(PK_SoundEntry) AS Quantity,
			Name                 AS Name
		FROM
			SoundFolders
			LEFT JOIN SoundEntries ON FK_SoundFolder = PK_SoundFolder
		GROUP BY PK_SoundFolder
		ORDER BY Name
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $SoundFolders[] = $row;
    }

    // Init table fields (SoundEntries)
    $SoundEntries = array();
    $query = "
		SELECT
			PK_SoundEntry        AS _PK_,
			PK_SoundEntry,
			SoundFiles.Name      AS Name,
			SoundFiles.Description AS Description,
			SoundFolders.Name    AS Folder,
			SoundLanguages.Name  AS Language,
			SoundPacks.Name      AS Pack,
			SoundEntries.Type    AS Type
		FROM
			SoundEntries
			INNER JOIN SoundFolders   ON FK_SoundFolder   = PK_SoundFolder
			INNER JOIN SoundFiles     ON FK_SoundEntry    = PK_SoundEntry
			INNER JOIN SoundLanguages ON FK_SoundLanguage = PK_SoundLanguage
			LEFT  JOIN SoundPacks     ON FK_SoundPack     = PK_SoundPack
		" .
            (empty($PK_SoundFolder) ? '' : "
			WHERE
				PK_SoundFolder = {$PK_SoundFolder}
			")
            . "
		GROUP BY
			PK_SoundEntry
		ORDER BY
			$Sort $Order
		LIMIT $Start, $PageSize
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $SoundEntries[] = $row;
    }

    // Init end record (End)
    $End = $Start + count($SoundEntries);

    $smarty->assign('SoundFolders', $SoundFolders);
    $smarty->assign('SoundEntries', $SoundEntries);
    $smarty->assign('PK_SoundFolder', $PK_SoundFolder);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Start', $Start);
    $smarty->assign('End', $End);
    $smarty->assign('Total', $Total);
    $smarty->assign('PageSize', $PageSize);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('SoundEntries_List.tpl');
}

admin_run('SoundEntries_List', 'Admin.tpl');
?>