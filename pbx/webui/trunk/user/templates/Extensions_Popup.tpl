<script type="text/javascript">
{literal}
function Fill() {
	$("#{/literal}{$FillID}{literal}", opener.document).val($("#Extensions").val());
	$("#{/literal}{$FillID}{literal}", opener.document).focus();
	window.close();
}
{/literal}
</script>

<small>Choose an Extension:</small>
<br />

<select multiple="multiple" style="width: 100%; height: 250px" id="Extensions">
{foreach from=$Extensions item=Extension}
	<option value="{$Extension.Extension}">{$Extension.Extension} -
		{if     $Extension.Type == 'SipPhone'   } SIP Extension
		{elseif $Extension.Type == 'Queue'      } Call Queue
		{elseif $Extension.Type == 'Virtual'    } Virtual Extension
		{elseif $Extension.Type == 'IVR'        } IVR
		{elseif $Extension.Type == 'AgentLogin' } Agent Login
		{elseif $Extension.Type == 'Voicemail'  } Voicemail
		{elseif $Extension.Type == 'SimpleConf' } Simple Conference
		{elseif $Extension.Type == 'ConfCenter' } Meet Me Conference Center
		{elseif $Extension.Type == 'DialTone'   } Dialtone
		{elseif $Extension.Type == 'ParkingLot' } Call Parking
		{else                                   } {$Extension.Type}
		{/if}
		-
		{$Extension.Name}
		{if     $Extension.Type == 'AgentLogin' } Agent Login
		{elseif $Extension.Type == 'Voicemail'  } Voicemail
		{elseif $Extension.Type == 'SimpleConf' } Conference
		{elseif $Extension.Type == 'ConfCenter' } Conference
		{elseif $Extension.Type == 'DialTone'   } Dialtone
		{elseif $Extension.Type == 'ParkingLot' } Call Parking
		{elseif $Extension.Type == 'Intercom'   } Intercom
		{/if}
	</option>
{/foreach}
</select>

<br />
<button onclick="Fill();">Choose Extension</button>