<h2>Manage Extensions</h2>
<br />
<p style="font-weight: bold;">
	Are you sure you want to permanently delete agent "{$Extension.Extension}"?
</p>
<br />

<p>
	<form method="post" action="Extensions_Agent_Delete.php">
		<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Extension</button>
	</form>
</p>