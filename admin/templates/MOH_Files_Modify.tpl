<h2>Music On Hold</h2>
{if $Errors.BadFilename}<p class="error_message">Bad file name.</p>{/if}
{if $Errors.FileToBig}<p class="error_message">File is to big.</p>{/if}

{if $Message == "UPLOAD_SUCCESSFULLY"}
	<p class="success_message"> Successfully created music on hold file.</p>
{/if}
		
<form action="MOH_Files_Modify.php?action=uploadfile" method="post" enctype="multipart/form-data">
	<table class="formtable">
		<tr>
			<td>
				Song to Upload <br> 
				<strong><small>Must be a mp3 or a ogg file</small></strong>
			</td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE"   />
				<input name="file[]" type="file" /><br />
			</td>
		</tr>
		<tr>
			<td>
				Part of Group
			</td>
			<td>
				<select name="id_group">
					{foreach from=$Groups item=Group}							
						<option value="{$Group.PK_Group}" {if $Group.Name == "default"}	selected	{/if}>
							{$Group.Name}
						</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<button name="upload" type="submit" value="Upload File" >Upload File</button>
			</td>	
		</tr>
	</table>
</form>
	