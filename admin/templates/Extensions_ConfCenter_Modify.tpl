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
{/literal}
</script>


<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 4 digits in length.</p>
{/if}
{if $Errors.TransferExt.Invalid}
<p class="error_message">Extension may only consist of digits and must be 4 digits in length.</p>
{/if}
{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>
{/if}

<form action="Extensions_ConfCenter_Modify.php" method="post" >
<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
<table class="formtable">
	<!-- Simple Conference Room Extension -->
	<tr>
		<td>
			Meet Me Conference Center Extension :						
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
			<input type="checkbox" name="IVRDial" id="IVRDial" value="1" {if $Extension.IVRDial=='1'}checked="checked"{/if} />
			<label for="IVRDial">This extension can be dialed from an IVR. </label>
		</td>
	</tr>

	
	<tr>
		<td>
			After
			<select name="Invalid" type="comboBox">
			    <option value="0">Unlinited</option>
			    <option value="1" {if $Extension.Invalid=='1'}Selected='selected'{/if}>1</option>
			    <option value="2" {if $Extension.Invalid=='2'}Selected='selected'{/if}>2</option>
			    <option value="3" {if $Extension.Invalid=='3'}Selected='selected'{/if}>3</option>
			    <option value="4" {if $Extension.Invalid=='4'}Selected='selected'{/if}>4</option>
			    <option value="5" {if $Extension.Invalid=='5'}Selected='selected'{/if}>5</option>
			    <option value="6" {if $Extension.Invalid=='6'}Selected='selected'{/if}>6</option>
			    <option value="7" {if $Extension.Invalid=='7'}Selected='selected'{/if}>7</option>
			</select>
			invalid conference room number attempts route caller to extension
			<input type="text" size="5" id="TransferExt" name="TransferExt" value="{$Extension.TransferExt}" {if $Errors.TransferExt}class="error"{/if} />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=TransferExt','Select Extension',415,400);">&nbsp;</button>
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