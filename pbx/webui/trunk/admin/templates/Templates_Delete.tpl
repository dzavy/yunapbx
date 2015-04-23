<h2>Extension Templates</h2>
<p style="color: #000; font-weight: bold;">
	Are you sure you want to permanently delete template "{$Template.Name}"?
</p>
<p>
	<form method="post" action="Templates_Delete.php">
		<input type="hidden" name="PK_Template" value="{$Template.PK_Template}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Template</button>
	</form>
</p>