<h2>Extension Groups</h2>

<p>
	<strong>Are you sure you want to permanently delete the extension group "{$Group.Name}"?</strong>
</p>
<br />
<p>
	Current Members of this Group:
	<table style="margin-left: 10px; ">
		{foreach from=$Group.Extensions item=Extension}
			<tr>
				<td>{$Extension.Extension}</td>
				<td>"{$Extension.FirstName} {$Extension.LastName}"</td>
			</tr>
		{/foreach}
	</table>
</p>
<br />
<p>
	<form method="post" action="Groups_Delete.php">
		<input type="hidden" name="PK_Group" value="{$Group.PK_Group}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Extension Group</button>
	</form>
</p>