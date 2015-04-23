<h2>Music On Hold</h2>

<p>
	Are you sure you want to permanently delete this music on hold group <b>{$Group.Name}</b> and all of the files in it?
</p>

<form method="post" action="MOH_Groups_Delete.php">
	<input type="hidden" name="PK_Group" value="{$Group.PK_Group}" />
	<button type="submit" name="submit" class="important" value="delete_confirm">Delete</button>
</form>

