<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 3-5 digits in length.</p>
{/if}
{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>
{/if}
{if $Errors.Password.Invalid}
<p class="error_message">A valid password is required and must only consist of digits and must be 3-10 digits in length.</p>
{/if}
{if $Errors.Password.Match}
<p class="error_message">Your two numeric passwords you entered did not match each other.</p>
{/if}
{if $Errors.FirstName.Invalid}
<p class="error_message"> First name is required and must be 1 - 32 characters in length.</p>
{/if}

<form action="Extensions_Agent_Modify.php" method="post" >
<p>
	<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
</p>

<br />

<!-- Extension Settings -->
<table class="formtable" style="margin-top: -20px;">

	<!-- Extension -->
	<tr>
		<td>Call Queue Agent Extension</td>
		<td>
			{if $Extension.PK_Extension != "" }
			{$Extension.Extension}
			<input type="hidden" name="Extension" value="{$Extension.Extension}" />
			{else}
			<input type="text" size="5" name="Extension" value="{$Extension.Extension}" {if $Errors.Extension}class="error"{/if} />
			{/if}
		</td>
	</tr>

	<!-- First Name -->
	<tr>
		<td>First Name</td>
		<td>
			<input type="text" name="FirstName" value="{$Extension.FirstName}" {if $Errors.FirstName }class="error"{/if} />&nbsp;
			<label for="FirstName_Editable">User can edit</label>
			<input type="checkbox" value="1" name="FirstName_Editable" id="FirstName_Editable" {if $Extension.FirstName_Editable}checked="checked"{/if} />
		</td>
	</tr>

	<!-- Last Name -->
	<tr>
		<td>Last Name</td>
		<td>
			<input type="text" name="LastName" value="{$Extension.LastName}" />&nbsp;
			<label for="LastName_Editable">User can edit</label>
			<input type="checkbox" value="1" name="LastName_Editable" id="LastName_Editable" {if $Extension.LastName_Editable}checked="checked"{/if} />
		</td>
	</tr>

	<!-- Numeric Password -->
	<tr>
		<td>
			Numeric Password<br/>
			<small>For web tool access and queue log in</small>
		</td>
		<td>
			<input type="password" name="Password" value="{$Extension.Password}" {if $Errors.Extension}class="error"{/if} />&nbsp;
			<label for="Password_Editable">User can edit</label>
			<input type="checkbox" value="1" name="Password_Editable" id="Password_Editable" {if $Extension.Password_Editable}checked="checked"{/if} />
		</td>
	</tr>

	<!-- Retype Numeric Password -->
	<tr>
		<td>
			Retype Numeric Password<br/>
			<small>Must match password above</small>
		</td>
		<td>
			<input type="password" name="Password_Retype" {if $Errors.Extension}class="error"{/if} />&nbsp;
		</td>
	</tr>

	<!-- Numeric Password -->
	<tr>
		<td><label for="WebAccess">Web Tool Access</label></td>
		<td>
			<input type="checkbox" value="1" name="WebAccess" id="WebAccess" {if $Extension.WebAccess}checked="checked"{/if} />
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	<br />
	<button type="submit" name="submit" value="save">Save Extension Settings</button>
</p>
</form>