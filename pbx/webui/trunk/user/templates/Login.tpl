<form method="post" action="Login.php" >
<table style="margin:auto; font-size: 11px;">
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="text-align: center;">
			<strong>Please Login to Use the Tool Suite</strong>
		</td>
	</tr>
	
	<tr><td>&nbsp;</td></tr>

	<tr>
		<td>Extension</td>
		<td>
			<input type="text" name="Extension" />
		</td>
	</tr>
	<tr><td></td></tr>
	<tr>
		<td>Password</td>
		<td>
			<input type="password" name="Password" />
		</td>
	</tr>
	
	<tr><td>&nbsp;</td></tr>
</table>
<table style="margin:auto; font-size: 11px;">
	<tr>
		<td colspan="2" style="text-align:center;">
			<button type="submit" value="submit" name="submit">Sign In</button>
		</td>
	</tr>
	{if $Errors}
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			{if $Errors.Extension.Invalid}
				<p class="error_message">Extension may only consist of digits and must be 3-5 digits in length.</p>
			{/if}
			{if $Errors.Wrong}
				<p class="error_message">Username or Password incorrect</p>
			{/if}
		</td>
	</tr>
	{/if}
</table>
</form>
