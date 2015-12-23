<h2>VOIP Providers</h2>
<br />
<p style="font-weight: bold;">
	Are you sure you want to permanently delete this SIP provider ({$Dongle.Name})?
</p>
<br />
<p>
	<form method="post" action="Dongles_Delete.php">
		<input type="hidden" name="PK_Dongle" value="{$Dongle.PK_Dongle}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Provider</button>
	</form>
</p>