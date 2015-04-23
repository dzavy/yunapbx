<h2>Manage Extensions</h2>
<br />
<p style="font-weight: bold;">
	Are you sure you want to permanently delete this call queue ({$Queue.Name})?
</p>
<br />

<p>
	<form method="post" action="Extensions_Queue_Delete.php">
		<input type="hidden" name="PK_Extension" value="{$Queue.PK_Extension}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Call Queue</button>
	</form>
</p>