<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 3-5 digits in length.</p>
{/if}
{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>
{/if}

<form action="Extensions_AgentLogin_Modify.php" method="post" >
<input type="hidden" name="PK_Extension" value="{$AgentLogin.PK_Extension}" />
<table class="formtable">
	<!-- Agent Login Extension -->
	<tr>
		<td>
			Agent Log In Extension
			{if $AgentLogin.PK_Extension != "" }
			<strong>{$AgentLogin.Extension}</strong>
			<input type="hidden" name="Extension" value="{$AgentLogin.Extension}" />
			{else}
			<input type="text" size="5" name="Extension" value="{$AgentLogin.Extension}" {if $Errors.Extension}class="error"{/if} />
			{/if}
		</td>
	</tr>

	<tr>
		<td>
			<input type="checkbox" name="RequirePassword" id="RequirePassword" value="0" {if $AgentLogin.RequirePassword==0}checked='checked'{/if} />
			<label for="RequirePassword">Do not require password to login</label>
			<br /><br />
			<input type="checkbox" name="LoginToggle" id="LoginToggle" value="1" {if $AgentLogin.LoginToggle}checked='checked'{/if} />
			<label for="LoginToggle">Login/Logout toggle</label>
			<br /><br />
			<input type="checkbox" name="EnterExtension" id="EnterExtension" value="1" {if $AgentLogin.EnterExtension}checked='checked'{/if} />
			<label for="EnterExtension">Always prompt agents to enter their login extension</label>
		</td>
	</tr>
	<tr>
		<td>
			{if $AgentLogin.PK_Extension != ""}
				<button type="submit" name="submit" value="save">Modify Extension</button>
			{else}
				<button type="submit" name="submit" value="save">Create Extension</button>
			{/if}
		</td>
	</tr>
</table>
</form>