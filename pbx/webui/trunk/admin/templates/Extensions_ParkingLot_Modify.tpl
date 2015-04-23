<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 3 to 5 digits in length.</p>
{/if}
{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>
{/if}
{if $Errors.Timeout.Invalid}
<p class="error_message">Your parking lot timeout must be 1-9999 seconds.</p>
{/if}
{if $Errors.Start.Invalid}
<p class="error_message">Parking lot start extension must be 3 to 5 digits in length.</p>
{/if}
{if $Errors.Stop.Invalid}
<p class="error_message">Parking lot stop extension must be 3 to 5 digits in length.</p>
{/if}
{if $Errors.Stop.TooSmall}
<p class="error_message">Parking lot stop extension it's smaller that start extension.</p>
{/if}
{if $Errors.Stop.Conflict}
<p class="error_message">
	The extension range ({$Extension.Start} - {$Extension.Stop}) you choose for our parking lot conflicts (matches) with some of your extensions in the system.Please visit the <a href="Extensions_List.php">Manage Extensions</a> section to fix these extensions or choose different call rule settings which do not conflict. The conflicting extensions are listed below.
</p>
<ul>
	{foreach from=$Errors.Conflicts item=Conflict}
	<li>{$Conflict}</li>
	{/foreach}
</ul>
<br />
{/if}
<form action="Extensions_ParkingLot_Modify.php" method="post" >
<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
<table class="formtable">
	<!-- Call Parking Extension -->
	<tr>
		<td>
			Call Parking Extension :
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
			Parking lot will reserve all extensions between
			<input type="text" size="5" name="Start" value="{$Extension.Start}" {if $Errors.Start}class="error"{/if} />
			through
			<input type="text" size="5" name="Stop" value="{$Extension.Stop}" {if $Errors.Stop}class="error"{/if} />.
		</td>
	</tr>

	<tr>
		<td>
			Return parked calls to the extension that parked the call after
			<input type="text" size="5" maxlength="4" name="Timeout" value="{$Extension.Timeout}" {if $Errors.Timeout}class="error"{/if} />
			seconds.
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