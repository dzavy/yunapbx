<h2>3G Dongles</h2>
<br />
<p style="font-weight: bold;">
	Are you sure you want to permanently delete this 3G dongle ({$Dongle.Name})?
</p>
<br />
<p>
	<form method="post" action="Dongles_Delete.php">
		<input type="hidden" name="PK_Dongle" value="{$Dongle.PK_Dongle}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete dongle</button>
	</form>
</p>