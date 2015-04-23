

<script language="javascript">
{literal}

{/literal}

</script>

{php}
print ("<pre>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> <br>");
print_r ($this->_tpl_vars);
print ("<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<</pre>");
{/php}

<h2>Upload Form</h2>
		
	<form action="MOH_Upload_Song.php?action=uploadfile" method="post" enctype="multipart/form-data">
	  <input type="hidden" name="MAX_FILE_SIZE"   />
	  <input name="file[]" type="file" /><br />
	  
	  <input name="upload" type="submit" value="Upload File" />
	</form>
	