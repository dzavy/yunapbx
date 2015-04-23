<h2>Backups</h2>

<p>
	<strong>Are you sure you want to permanently delete the backup created on {$Date}?</strong>
</p>
<br />

	

	<form method="post" action="Backup_Delete.php">
		<input type="hidden" name="PK_Backup" value='{$PK}' />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Backup</button>
	</form>
</p>