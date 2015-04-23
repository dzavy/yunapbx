<h2>IVR Editor</h2>

<p>
	<strong>Are you sure you want to permanently delete the "{$Option.Key}" option from this IVR menu?</strong>
</p>
<br />

<br />
<p>
	<form method="post" action="IVR_Options_Delete.php">
		<input type="hidden" name="PK_Option" value="{$Option.PK_Option}" />
		<input type="hidden" name="PK_Menu" value="{$Option.FK_Menu}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Option</button>
	</form>
</p>