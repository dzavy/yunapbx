<h2>VOIP Providers</h2>
<br />
<p style="font-weight: bold;">
	Are you sure you want to permanently delete this IAX provider ({$Provider.Name})?
</p>
<br />
<p>
	<form method="post" action="VoipProviders_Iax_Delete.php">
		<input type="hidden" name="PK_IaxProvider" value="{$Provider.PK_IaxProvider}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Provider</button>
	</form>
</p>