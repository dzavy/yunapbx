<script type="text/javascript">
{literal}
function ChangeListView() {
	document.location=$('#View').val();
}
{/literal}
</script>

<h2>Manage Extensions</h2>
<p>
	<button type="button" onclick="document.location='Extensions_Create.php'">Create A New Extension</button>
</p>

<table class="fullwidth" style="margin-bottom: 20px;">
	<tr>
		<td>
			<label for="View">View</label>
			<select id="View" name="View">
				<option value="Extensions_List.php" selected="selected" >All Extensions</option>
				<option value="Extensions_List_Phones.php"> Only Phones</option>
				<option value="Extensions_List_IVRs.php">   Only IVRs</option>
				<option value="Extensions_List_Queues.php"> Only Call Queues</option>
				<option value="Extensions_List_Agents.php"> Only Agents</option>
			</select>
			<button type="button" onclick="ChangeListView();"> Go</button>
		</td>
		<td style="text-align: right;">
			<form action="Extensions_List.php" method="get">
				<label for="Search">Search:</label>
				<input type="text" id="Search" name="Search" value="{$Search}" />
			</form>
		</td>
	</tr>
</table>

{if $Message != ""}
<p class="success_message">
{if     $Message == "MODIFY_SIPPHONE_EXTENSION"   } Successfully modified phone extension.
{elseif $Message == "DELETE_SIPPHONE_EXTENSION"   } Successfully deleted phone extension.

{elseif $Message == "MODIFY_VIRTUAL_EXTENSION" } Successfully modified virtual extension.
{elseif $Message == "DELETE_VIRTUAL_EXTENSION" } Successfully deleted virtual extension.

{elseif $Message == "MODIFY_QUEUE_EXTENSION"   } Successfully modified call queue.
{elseif $Message == "DELETE_QUEUE_EXTENSION"   } Successfully deleted call queue.

{elseif $Message == "MODIFY_AGENT_EXTENSION"   } Successfully modified agent extension.
{elseif $Message == "DELETE_AGENT_EXTENSION"   } Successfully deleted agent extension.

{elseif $Message == "ADD_IVR_EXTENSION"      } Successfully created new IVR extension.
{elseif $Message == "MODIFY_IVR_EXTENSION"   } Successfully modified IVR extension.
{elseif $Message == "DELETE_IVR_EXTENSION"   } Successfully deleted IVR extension.

{elseif $Message == "ADD_VOICEMAIL_EXTENSION"     } Successfully created new voicemail extension.
{elseif $Message == "MODIFY_VOICEMAIL_EXTENSION"  } Successfully modified voicemail extension.
{elseif $Message == "DELETE_VOICEMAIL_EXTENSION"  } Successfully deleted voicemail extension.

{elseif $Message == "ADD_DIALTONE_EXTENSION"    } Successfully created dial tone extension.
{elseif $Message == "MODIFY_DIALTONE_EXTENSION" } Successfully modified dial tone extension.
{elseif $Message == "DELETE_DIALTONE_EXTENSION" } Successfully deleted dial tone extension.

{elseif $Message == "ADD_SIMPLECONF_EXTENSION"     } Successfully created new simple conference extension.
{elseif $Message == "MODIFY_SIMPLECONF_EXTENSION"  } Successfully modified simple conference extension.
{elseif $Message == "DELETE_SIMPLECONF_EXTENSION"  } Successfully deleted simple conference extension.

{elseif $Message == "ADD_CONFCENTER_EXTENSION"     } Successfully created new conference center extension.
{elseif $Message == "MODIFY_CONFCENTER_EXTENSION"  } Successfully modified conference center extension.
{elseif $Message == "DELETE_CONFCENTER_EXTENSION"  } Successfully deleted conference center extension.

{elseif $Message == "ADD_AGENTLOGIN_EXTENSION"    } Successfully created new agent login extension.
{elseif $Message == "MODIFY_AGENTLOGIN_EXTENSION" } Successfully modified agent login extension.
{elseif $Message == "DELETE_AGENTLOGIN_EXTENSION" } Successfully deleted agent login extension.

{elseif $Message == "ADD_PARKINGLOT_EXTENSION"    } Successfully created callpark extension.
{elseif $Message == "MODIFY_PARKINGLOT_EXTENSION" } Successfully modified callpark extension.
{elseif $Message == "DELETE_PARKINGLOT_EXTENSION" } Successfully deleted callpark extension.

{elseif $Message == "ADD_INTERCOM_EXTENSION"    } Successfully created new intercom extension.
{elseif $Message == "MODIFY_INTERCOM_EXTENSION" } Successfully modified intercom extension.
{elseif $Message == "DELETE_INTERCOM_EXTENSION" } Successfully deleted intercom extension.

{elseif $Message == "ADD_FC_VOICEMAIL_EXTENSION"    } Successfully created new feature code - voicemail extension.
{elseif $Message == "MODIFY_FC_VOICEMAIL_EXTENSION" } Successfully modified feature code - voicemail extension.
{elseif $Message == "DELETE_FC_VOICEMAIL_EXTENSION" } Successfully deleted feature code - voicemail extension.

{elseif $Message == "ADD_FC_DIRECTEDPICKUP_EXTENSION"    } Successfully created new feature code - directed pickup extension.
{elseif $Message == "MODIFY_FC_DIRECTEDPICKUP_EXTENSION" } Successfully modified feature code - directed pickup extension.
{elseif $Message == "DELETE_FC_DIRECTEDPICKUP_EXTENSION" } Successfully deleted feature code - directed pickup extension.

{elseif $Message == "ADD_FC_CALLMONITOR_EXTENSION"    } Successfully created new feature code - call monitor extension.
{elseif $Message == "MODIFY_FC_CALLMONITOR_EXTENSION" } Successfully modified feature code - call monitor extension.
{elseif $Message == "DELETE_FC_CALLMONITOR_EXTENSION" } Successfully deleted feature code - call monitor extension.

{elseif $Message == "ADD_FC_INTERCOM_EXTENSION"    } Successfully created new feature code - personal intercom extension.
{elseif $Message == "MODIFY_FC_INTERCOM_EXTENSION" } Successfully modified feature code - personal intercom extension.
{elseif $Message == "DELETE_FC_INTERCOM_EXTENSION" } Successfully deleted feature code - personal intercom extension.

{else } {$Message} {/if}
</p>
{/if}

{if $ErrMessage == "CONFLICT_PARKINGLOT_EXTENSION"}
<p class="error_message">Only one Call Parking extension per system is allowed. Please delete or modify your existing Call Parking extension.</p>
{/if}



{if $Extensions|@count }
<table class="listing fullwidth">
	<caption>All Extensions ( {$Start+1} to {$End} ) of {$Total}</caption>
	<thead>
	<tr>
		<th>
			<a href="?Sort=Extension">Extension</a>
			{if $Sort == "Extension"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Type">Extension Type</a>
			{if $Sort == "Type"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Name">Name</a>
			{if $Sort == "Name"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th style="width: 120px"></th>
	</tr>
	</thead>
	{foreach from=$Extensions item=Extension}
	<tr class="{if $Hilight == $Extension._PK_}hilight{/if} {cycle values='odd,even'}">
		<td>{if $Extension.Feature == 1}*{/if}{$Extension.Extension}</td>
		<td>
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
			{elseif $Extension.Type == 'GroupPickup'} Group Pickup
			{elseif $Extension.Type == 'Directory'  } Directory
			{elseif $Extension.Feature == 1         } Feature Code
			{else                                   } {$Extension.Type}
			{/if}
		</td>
		<td>
			{$Extension.Name}
			{if $Extension.Type == 'FC_Voicemail'     } Send to Voicemail
			{elseif $Extension.Type == 'FC_DirectedPickup'} Directed Pickup
			{elseif $Extension.Type == 'FC_CallMonitor'   } Call Monitor
			{elseif $Extension.Type == 'FC_Intercom'      } Personal Intercom
			{/if}
		</td>
		<td style="width: 120px">
			<form method="get" action="Extensions_{$Extension.Type}_Modify.php" style="display: inline;">
				<input type="hidden" name="PK_Extension" value="{$Extension._PK_}" />
				<button type="submit">Modify</button>
			</form>
			<form method="get" action="Extensions_{$Extension.Type}_Delete.php" style="display: inline;">
				<input type="hidden" name="PK_Extension" value="{$Extension._PK_}" />
				<button type="submit" class="important">Delete</button>
			</form>
		</td>
	</tr>
	{/foreach}
</table>
{else}
<p class="warning_message">
	There are no extensions defined on this system. 
	Use the <em>Create New Extension</em> button to define one now.
</p>
{/if}

<p style="text-align: right">
{if $Start > 0}
	<a class="prev" href="?Start = {$Start - $PageSize}">Previous</a>
{/if}
{if $End < $Total}
<a class="next" href="?Start = {$Start + $PageSize}">Next</a>
{/if}
</p>
