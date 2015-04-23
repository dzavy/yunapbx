<h2>Time Frames</h2>
{if $Message == "DELETE_TIMEFRAME"}
<p class="success_message">Successfully deleted Time Frame.</p>
{/if}
{if $Errors.Name}
<p class="error_message">Time Frame Name is missing or too long (1-30 chars)</p>
{/if}

<p>
	Create, modify and delete time frames to facilitate the administration of call rules on your PBX.
</p>

<h2>Create A New Time Frame</h2>

<form action="TimeFrames.php" method="post">
<p>
	Time Frame Name
	<input type="text" name="Name" />
	<button type="submit" name="sumbit" value="Create">Create New</button>
</p>
</form>
<br />

<h2>Modify Existing Time Frames</h2>
<table class="listing">
	{foreach from=$Timeframes item=Timeframe}
	<tr>
		<td style="padding-right: 10px; font-size: 11px;">{$Timeframe.Name}</td>
		<td>
			<form action="TimeFrames_Modify.php?FK_Timeframe={$Timeframe._PK_}" method="post">
			<button type="submit">Edit</button>
			</form>
		</td>
		<td>
			<form action="TimeFrames_Delete.php?PK_Timeframe={$Timeframe._PK_}" method="post">
			<button class="important" type="submit">Delete</button>
			</form>
		</td>
	</tr>
	{/foreach}
</table>