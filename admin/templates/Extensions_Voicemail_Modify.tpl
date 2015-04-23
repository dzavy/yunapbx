<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 3-5 digits in length.</p>
{/if}
{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>
{/if}

<form action="Extensions_Voicemail_Modify.php" method="post" >
<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
<table class="formtable">
	<!-- Agent Login Extension -->
	<tr>
		<td>
			Voicemail Access Extension :
			{if $Extension.PK_Extension != "" }
			<strong>{$Extension.Extension}</strong>
			<input type="hidden" name="Extension" value="{$Extension.Extension}" />
			{else}
			<input type="text" size="5" name="Extension" value="{$Extension.Extension}" {if $Errors.Extension}class="error"{/if} />
			{/if}
		</td>
	</tr>

	<tr>
		<td>
			<input type="checkbox" name="IVRDial" id="IVRDial" value="1" {if $Extension.IVRDial=='1'}checked="checked"{/if} />
			<label for="IVRDial">This extension can be dialed from an IVR.</label>
		</td>
	</tr>

	<tr>
		<td>
			<input type="checkbox" name="RequirePassword" id="RequirePassword" value="0" {if $Extension.RequirePassword==0}checked='checked'{/if} />
			<label for="RequirePassword">Do not require password as long as caller is calling from their own extension. </label>
		</td>
	</tr>

	<tr>
		<td>
			{if $Extension.PK_Extension != "" }
				<button type="submit" name="submit" value="save">Modify Extension</button>
			{else}
				<button type="submit" name="submit" value="save">Create Extension</button>
			{/if}
		</td>
	</tr>
</table>
</form>