<br />
<p>
	<strong>Action Type:</strong>
	{include file="IVR_Actions_Display.tpl" Display="Name" Action=$IVR_Action}
</p>

<br />
<p style="text-align: center">
	<form action="IVR_Actions_Modify.{$IVR_Action.Type}.php" method="post" style="display:inline">
		<input type="hidden" name="PK_Action" value="{$IVR_Action.PK_Action}" />
		<button type="submit">Modify This Action</button>
	</form>
	<button class="important">Delete This Action</button>
</p>