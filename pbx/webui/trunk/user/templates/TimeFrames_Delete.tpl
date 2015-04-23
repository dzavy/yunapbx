<h2>Time Frames</h2>
<p style="color: #000; font-weight: bold;">
	Are you sure you want to permanently delete this Time Frame ({$Timeframe.Name})?
</p>
<p>
	<form method="post" action="TimeFrames_Delete.php">
		<input type="hidden" name="PK_Timeframe" value="{$Timeframe.PK_Timeframe}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Time Frame</button>
	</form>
</p>