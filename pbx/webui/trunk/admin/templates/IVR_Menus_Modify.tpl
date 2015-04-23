<h2>IVR Editor</h2>

<form action="IVR_Menus_Modify.php" method="post">
<p>
	<input type="hidden" name="PK_Menu" value="{$Menu.PK_Menu}" />
</p>

<table class="formtable">
	<!-- IVR Menu Name -->
	<tr>
		<td>IVR Menu Name:</td>
		<td>
			<input type="text" name="Name" value="{$Menu.Name}" {if $Errors.Name}class="error"{/if} />
		</td>
	</tr>

	<!-- Description -->
	<tr>
		<td>Description:</td>
		<td>
			<textarea name="Description" {if $Errors.Description}class="error"{/if} cols="30" rows="5">{$Menu.Description}</textarea>
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	<br />
	<button type="submit" name="submit" value="save">Update IVR Menu</button>
</p>
</form>