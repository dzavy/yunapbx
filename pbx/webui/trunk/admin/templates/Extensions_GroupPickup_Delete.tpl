<h2>Manage Extensions</h2>
<br />
<p style="font-weight: bold;">
	Are you sure you want to permanently delete this GroupPickup extension ({$Extension.Extension}) ?
</p>
<br />


<form method="post" action="Extensions_GroupPickup_Delete.php">
<p>
		<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Extension</button>
</p>
</form>