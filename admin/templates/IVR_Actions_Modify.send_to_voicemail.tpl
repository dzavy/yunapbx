<script type="text/javascript">
{literal}
	function popUp(url,inName,width,height)
	{
		inName = inName.replace(/ /g, "_"); /* For stupid pos IE */
		var popup = window.open('',inName,'width='+width+',height='+height+',toolbars=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,left=200,top=200');

		// only reload the page if it contains a new url
		if (popup.closed || !popup.document.URL || (-1 == popup.document.URL.indexOf(url)))
		{
			popup.location = url;
		}
		popup.focus();
		return popup;
	}
	function Update_UseVariable_Status() {
		if ($("#Param_Extension").val() != "") {
			$("#UseVariable_0").attr('checked','checked');
			$("#UseVariable_0").removeAttr('disabled');
			$("#UseVariable_1").removeAttr('checked','checked');
			$("#UseVariable_1").attr('disabled','disabled');
			$("#Var_Extension").attr('disabled','disable');
		} else {
			$("#UseVariable_1").attr('checked','checked');
			$("#UseVariable_1").removeAttr('disabled');
			$("#UseVariable_0").removeAttr('checked','checked');
			$("#UseVariable_0").attr('disabled','disabled');
			$("#Var_Extension").removeAttr('disabled');
		}
	}

	$(document).ready(function() {
		Update_UseVariable_Status();
		$("#Param_Extension").change(Update_UseVariable_Status);
		$("#Param_Extension").keyup(Update_UseVariable_Status);
		$("#Param_Extension").focus(Update_UseVariable_Status);
	})
{/literal}
</script>
<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Send to Voicemail</h2>
{else}
	<h2>Modify Action: Send to Voicemail</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.send_to_voicemail.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />


<table class="formtable">
	<tr>
		<td>
			<input type="radio" name="UseVariable" id="UseVariable_1" value="1" />
			From Variable
		</td>
		<td>
			<select id="Var_Extension" name="Var[Extension]">
				{foreach from=$Variables item=Variable}
				<option value="{$Variable}" {if $Action.Var.Extension==$Variable}selected="selected"{/if}>{$Variable}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<input type="radio" name="UseVariable" id="UseVariable_0" value="0" />
			Manualy Entered
		</td>
		<td>
			<input type="text" id="Param_Extension" name="Param[Extension]" value="{$Action.Param.Extension}" size="6" />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=Param_Extension','Select Extension',415,400);">&nbsp;</button>
		</td>
	</tr>
</table>

<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>