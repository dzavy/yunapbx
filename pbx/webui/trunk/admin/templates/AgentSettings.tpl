<h2>Agent Settings</h2>
{if $Errors.Agent_MissedCalls.Invalid}
<p class="error_message">Number of missed calls must be a number between 0-999.</p>
{/if}
<p>
	Customize the settings for your call queue agents. If you have multiple call queues, these settings apply to all of them.
</p>

<form action="AgentSettings.php" method="post" >

<table class="formtable">

	<tr>
		<td>
			<label for="IVRDial">Acknowledge Call</label>
		</td>
		<td>
			<input type="radio" name="Agent_AckCall" value="1" {if $Settings.Agent_AckCall=='1'}checked{/if}> Yes &nbsp;&nbsp;&nbsp;
			<input type="radio" name="Agent_AckCall" value="0" {if $Settings.Agent_AckCall=='0'}checked{/if}> No
		</td>
	</tr>

	<tr>
		<td>
			<label >Number of Missed Calls Before Auto Log Off</label>
		</td>
		<td>
			<input type="text" size="5" name="Agent_MissedCalls" {if $Errors.Agent_MissedCalls}class="error"{/if} value="{$Settings.Agent_MissedCalls}" />
		</td>
	</tr>

	<tr>
		<td>
			<button type="submit" name="submit" value="save">Save Agent Settings</button>
		</td>
	</tr>

</table>
</form>