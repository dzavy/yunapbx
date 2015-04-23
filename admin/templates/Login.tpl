<form method="post" action="Login.php" >
<table style="margin:auto; font-size: 11px;">
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="text-align: center;">
			<strong>Please Login to Use the Admin Suite</strong>
		</td>
	</tr>
	
	<tr><td colspan="2">&nbsp;</td></tr>

	<tr>
		<td>User</td>
		<td>
			<input type="text" name="User" />
		</td>
	</tr>
	<tr>
		<td>Password</td>
		<td>
			<input type="password" name="Password" />
		</td>
	</tr>
	
        <tr><td colspan="2">&nbsp;</td></tr>
</table>
<table style="margin:auto; font-size: 11px;">
	<tr>
		<td colspan="2" style="text-align:center;">
			<button type="submit" value="submit" name="submit">Sign In</button>
		</td>
	</tr>
	{if $Errors}
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan="2">		
			{if $Errors.Wrong}
				<p class="error_message">Username or Password incorrect</p>
			{/if}
		</td>
	</tr>
	{/if}
</table>
</form>
