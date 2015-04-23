<?php        
include_once(dirname(__FILE__).'/../include/config.inc.php');

function delete_file($PK_File){
	$errors = array();
	
	//get information about source file (File_Src) 
	$query  = " 
		SELECT 
			`PK_File`, 
			`Order`, 
			`Fileext`, 
			`FK_Group` 
		FROM 
			Moh_Files 
		WHERE 
			PK_File = '".mysql_real_escape_string($PK_File)."'"
	;
	
	$result = mysql_query($query) or die(mysql_error().$query);
	$File_Src = mysql_fetch_assoc($result);	
	
	
	//get Pk_File and Order for reorder remained files on disk
	$query = "	
		SELECT 
			`Order`, `PK_File`, `Fileext`  
		FROM 
			Moh_Files
		WHERE
			`FK_Group` = '".intval($File_Src['FK_Group'])."'
		AND `Order` >".intval($File_Src['Order'])
	;		
	$result = mysql_query($query) or die(mysql_error().$query);
	$inf_files = mysql_fetch_all($result);	
	
	//reorder files on disk
	for($i=0; $i<count($inf_files); $i++){
			$src_filename = moh_filename($inf_files[$i]['PK_File'], $File_Src['FK_Group'], $inf_files[$i]['Order']  , $inf_files[$i]['Fileext']);
			$dst_filename = moh_filename($inf_files[$i]['PK_File'], $File_Src['FK_Group'], $inf_files[$i]['Order']-1, $inf_files[$i]['Fileext']);
					
			$result = rename ($src_filename, $dst_filename);	
			if (!$result){
				$errors['Delete']['ReorderFiles'] = true;
				return $errors;
			}	
	}
	
	//re-order files in source group - database
	$query  = "
		UPDATE
			Moh_Files 
		SET 
			`Order` = `Order`- 1
		WHERE 
			`Order` >'".intval($File_Src['Order'])."'
		AND  
			FK_Group = '".intval($File_Src['FK_Group'])."'";
	
	mysql_query($query) or die(mysql_error().$query);
		
	//del from hdd
	$del_file = moh_filename($PK_File, $File_Src['FK_Group'], $File_Src['Order'], $File_Src['Fileext']);	
	if (file_exists($del_file)) {
		$result = unlink($del_file);		
		if (!$result) {			
			$error['Delete']['File'] = true;
			return $error; 
		} 
	}	
	
	//del from database
	$query = "DELETE FROM Moh_Files WHERE PK_File = '".intval($PK_File)."'" ;
	mysql_query($query) or die(mysql_error().$query);
		
	return $errors;	
}

function move_file($PK_File, $dst_PK_Group){
	$errors = array();
	
	//get information about source file (File_Src)
	$query  = " 
		SELECT 
			`PK_File`, 
			`Filename`, 
			`Order`, 
			`Fileext`, 
			`FK_Group` 
		FROM 
			Moh_Files 
		WHERE 
			PK_File = '".mysql_real_escape_string($PK_File)."'"
	;

	$result = mysql_query($query) or die(mysql_error().$query);
	$File_Src = mysql_fetch_assoc($result);	
		
	//check if source group is different than destination group
	if ($File_Src['FK_Group'] == $dst_PK_Group) {
		$errors['Move']['InSameGroup'] = true; 
		return $errors;
	}	
		
	//get order for moved file in destionation group ($dst_order) 
	$query  = "SELECT MAX(`Order`) FROM Moh_Files WHERE FK_Group = '".intval($dst_PK_Group)."'";
	$result = mysql_query($query) or die(mysql_error());
	$row    = mysql_fetch_row($result);
	$dst_order = $row['0']+1;	
	
		// Check for duplicate files in the destination group
	$query = "
		SELECT 
			COUNT(*) 
		FROM 
			Moh_Files
		WHERE 
			`Filename` = '".mysql_real_escape_string($File_Src['Filename'])."'
			AND 
			`Fileext` = '".mysql_real_escape_string($File_Src['Fileext'])."'
			AND 
			`FK_Group` = '".mysql_real_escape_string($dst_PK_Group)."'
	";
		
	$result = mysql_query($query) or die ( mysql_error() );
	$rec = mysql_fetch_row($result);
	if  ($rec['0'])	{
		$errors['Move']['DuplicateFile'] = true;		
		return $errors;
	}
	
	
	

	//'move' file in database - change group, set order in new group
	$query  = "
		UPDATE
			Moh_Files 
		SET 
			`Order` = ".$dst_order.",
			`FK_Group` = ".$dst_PK_Group."
		WHERE 
			`PK_File` ='".intval($PK_File)."'";	
	
	mysql_query($query) or die(mysql_error().$query);
	
	//get Pk_File and Order for reorder remained files on disk
	$query = "	
		SELECT 
			`Order`, `PK_File`, `Fileext`  
		FROM 
			Moh_Files
		WHERE
			`FK_Group` = '".intval($File_Src['FK_Group'])."'
		AND `Order` >'".intval($File_Src['Order'])."'"
	;
	$result = mysql_query($query) or die(mysql_error().$query);
	
	//re-order files in source group - database
	$query  = "
		UPDATE
			Moh_Files 
		SET 
			`Order` = `Order`- 1
		WHERE 
			`Order` >'".intval($File_Src['Order'])."'
		AND  
			FK_Group = '".intval($File_Src['FK_Group'])."'";
	mysql_query($query) or die(mysql_error().$query);
	
	
	//move on hdd
	$src_filename = moh_filename($File_Src['PK_File'], $File_Src['FK_Group'], $File_Src['Order'], $File_Src['Fileext']);
	$dst_filename = moh_filename($File_Src['PK_File'], $dst_PK_Group        , $dst_order        , $File_Src['Fileext']);
		
	$move_result = rename($src_filename, $dst_filename);
	
	if (!$move_result) {
		$errors['Move']['File'] = true ;
		return $errors;
	}

    //reorder files in source group
	$res = mysql_fetch_all($result);
	
	for($i=0; $i<count($res); $i++){
		$src_filename = moh_filename($res[$i]['PK_File'], $File_Src['FK_Group'], $res[$i]['Order']  , $res[$i]['Fileext']);
		$dst_filename = moh_filename($res[$i]['PK_File'], $File_Src['FK_Group'], $res[$i]['Order']-1, $res[$i]['Fileext']);
					
		$result = rename($src_filename, $dst_filename);
		if (!$result){
			$errors['Move']['ReorderFiles'] = true;
			return $errors;
		}
	}
	
	return $errors;	
}
		
function copy_file($PK_File, $dest_PK_Group){
	$errors = array();
	
	// Get information about the source file (File_Src)
	$query  = "
		SELECT
			PK_File,
			`Order`, 
			`Fileext`, 
			`Filename`, 
			`FK_Group`
		FROM 
			Moh_Files 
		WHERE 
			PK_File = '".mysql_real_escape_string($PK_File)."'
	";
	$result   = mysql_query($query) or die(mysql_error().$query);
	$File_Src = mysql_fetch_assoc($result);
	
	// Check if the source group is different than the dest group
	if ($File_Src['FK_Group'] == $dest_PK_Group) {
		$errors['Copy']['SameDirectory'] = true; 
		return $errors;
	}	
	
	// Check for duplicate files in the destination group
	$query = "
		SELECT 
			COUNT(*) 
		FROM 
			Moh_Files
		WHERE 
			`Filename` = '".mysql_real_escape_string($File_Src['Filename'])."'
			AND 
			`Fileext` = '".mysql_real_escape_string($File_Src['Fileext'])."'
			AND 
			`FK_Group` = '".mysql_real_escape_string($dest_PK_Group)."'
	";
	$result = mysql_query($query) or die ( mysql_error() );
	$rec = mysql_fetch_row($result);
	if  ($rec['0'])	{
		$errors['Copy']['DuplicateFile'] = true;		
		return $errors;
	}
	
	// Get the next order for the destination of the file (copy_order)
	$query  = " SELECT MAX(`Order`) FROM Moh_Files WHERE FK_Group = '".mysql_real_escape_string($dest_PK_Group)."'";
	$result = mysql_query($query) or die(mysql_error());
	$row    = mysql_fetch_row($result);
	$copy_order = $row['0']+1;		
	
	//'copiere' in db
	$query =  "
		INSERT INTO	
			Moh_Files
		SET
			`Filename` = '".mysql_real_escape_string($File_Src['Filename'])."',
			`Fileext`  = '".mysql_real_escape_string($File_Src['Fileext'])."',
			`FK_Group` = '".intval($dest_PK_Group)."',
			`Order`    = ".intval($copy_order)."
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$copy_PK_File = mysql_insert_id();
	
	
	//copiere pe hdd
	$src_filename = moh_filename($File_Src['PK_File'], $File_Src['FK_Group'], $File_Src['Order'], $File_Src['Fileext']);
	$dst_filename = moh_filename($copy_PK_File       , $dest_PK_Group       , $copy_order       , $File_Src['Fileext']);
	
	$result = copy ($src_filename, $dst_filename);
	if (!$result) {
		$errors['Copy']['FileToDisk'] = true;
		return $errors;
	}
	return $errors;
}

function moh_filename($PK_File, $PK_Group='', $Order='', $Extension='') {
	include(dirname(__FILE__).'/../include/config.inc.php');

	if ("{$PK_Group}{$Order}{$Extension}" == "") {
		$query  = "SELECT * FROM Moh_Files WHERE PK_File = '".intval($PK_File)."' LIMIT 1";
		$result = mysql_query($query) or die(mysql_error().$query);
		$row    = mysql_fetch_assoc($result);
		
		$PK_Group = $row['FK_Group'];
		$Order    = $row['Order'];
		$Extension= $row['Fileext'];
	}
	
	$filename .= "group_".str_pad($PK_Group,10, "0", STR_PAD_LEFT);
	$filename .= "/file_" .str_pad($Order   , 6, "0", STR_PAD_LEFT);
	$filename .= "_".      str_pad($PK_File , 9, "0", STR_PAD_LEFT);
	$filename .= ".".$Extension;
	
	return $conf['dirs']['moh']."/$filename";
}

function mysql_fetch_all($result) {
   while($row=mysql_fetch_assoc($result)) {
       $return[] = $row;
   }
   return $return;
}
