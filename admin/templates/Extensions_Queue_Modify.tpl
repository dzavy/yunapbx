<script type="text/javascript" src="../static/script/jquery.selectboxes.js"></script>
<script type="text/javascript">
{literal}

function AddLoginMember() {
	var values = $("#AvailableMembers").selectedValues();

	$("#AvailableMembers").copyOptions("#Members");
	$("#AvailableMembers").removeOption(/./, true);
	$("#Members").selectOptions("",true);

	for (i=0;i<values.length;i++) {
		$("#Members > *[value='"+values[i]+"']").addClass('nonperm');
		$("#Members > *[value='"+values[i]+"']").val("1~"+values[i]);
	}
}

function AddPermanentMember() {
	var values = $("#AvailableMembers").selectedValues();

	$("#AvailableMembers").copyOptions("#Members");
	$("#AvailableMembers").removeOption(/./, true);
	$("#Members").selectOptions("",true);

	for (i=0;i<values.length;i++) {
		$("#Members > *[value='"+values[i]+"']").addClass('perm');
		$("#Members > *[value='"+values[i]+"']").val("0~"+values[i]);
	}
}

function swapOptions(obj,i,j) {
  var o = obj.options;
  if (i<0 || i>=o.length || j<0 || j>=o.length) { return false; }
  var i_selected = o[i].selected;
  var j_selected = o[j].selected;
  var i_class    = o[i].className;
  var j_class    = o[j].className;
  var temp = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
  var temp2= new Option(o[j].text, o[j].value, o[j].defaultSelected, o[j].selected);
  o[i] = temp2;
  o[j] = temp;
  o[i].selected = j_selected;
  o[j].selected = i_selected;
  o[i].className = j_class;
  o[j].className = i_class;
};

function MemberUp() {
	var obj = document.getElementById("Members");
	for (i=0; i<obj.options.length; i++) {
		if (obj.options[i].selected) {
			if (i>0 && !obj.options[i-1].selected) {
        		swapOptions(obj,i,i-1);
        		obj.options[i-1].selected = true;
			}
		}
	}
}

function MemberDown() {
	var obj = document.getElementById("Members");
	for (i=obj.options.length-1; i>=0; i--) {
		if (obj.options[i].selected) {
			if (i != (obj.options.length-1) && ! obj.options[i+1].selected) {
				this.swapOptions(obj,i,i+1);
				obj.options[i+1].selected = true;
			}
		}
	}
}

function RemoveMember() {
	var values = $("#Members").selectedValues();

	$("#Members").copyOptions("#AvailableMembers");
	$("#Members").removeOption(/./, true);
	$("#AvailableMembers").sortOptions(true);
	for (i=0;i<values.length;i++) {
		new_value = values[i].replace(/[0,1]~/,'');
		$("#AvailableMembers > *[value='"+values[i]+"']").val(new_value);
	}
}

function MemberTop() {
	var obj = document.getElementById("Members");
	for (j=0; j<=obj.options.length; j++) {
		MemberUp();
	}
}

function MemberBottom() {
	var obj = document.getElementById("Members");
	for (j=0; j<=obj.options.length; j++) {
		MemberDown();
	}
}
function PreSubmit() {
	$("#Members").selectOptions(/./, true);
}

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

function MusicInQueueOptions() {
	if ($("#PlayMohInQueue_0")[0].checked) {
		$('.moh').addClass('disabled');
		$('.moh input').attr("disabled", true);
		$('.moh select').attr("disabled", true);
	} else {
		$('.moh').removeClass('disabled');
		$('.moh input').attr("disabled", false);
		$('.moh select').attr("disabled", false);
	}
}

$(document).ready(function() {
	MusicInQueueOptions();
});
{/literal}
</script>

<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 3-5 digits in length.</p>{/if}

{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>{/if}

{if $Errors.Name.Invalid}
<p class="error_message">Queue name may only contain alpha-numeric characters or spaces and must be 1-20 characters in length.</p>{/if}

<form action="Extensions_Queue_Modify.php" method="post" onsubmit="PreSubmit()">
<p>
	<input type="hidden" name="PK_Extension" value="{$Queue.PK_Extension}" />
</p>

<!-- Call Queue Setup -->
<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/1.gif"/></td>
				<td>
					Call Queue Setup
				</td>
			</tr>
		</table>
	</td>
	</tr>


	<!-- Queue Extension -->
	<tr>
		<td>
			Queue Extension
		</td>
		<td>
			{if $Queue.PK_Extension != "" }
			{$Queue.Extension}
			<input type="hidden" name="Extension" value="{$Queue.Extension}" />
			{else}
			<input type="text" size="5" name="Extension" value="{$Queue.Extension}" {if $Errors.Extension}class="error"{/if} />
			{/if}
		</td>
	</tr>

	<!-- Queue Name -->
	<tr>
		<td>
			Queue Name<br/>
			<small>E.g. Tech Support</small>
		</td>
		<td>
			<input type="text" name="Name" value="{$Queue.Name}" {if $Errors.Name }class="error"{/if} />
		</td>
	</tr>

	<!-- Ringing Strategy -->
	<tr>
		<td>
			Ringing Strategy
		</td>
		<td>
			<select name="FK_RingStrategy">
			{foreach from=$RingStrategies item=Strategy}
				<option {if $Strategy.PK_RingStrategy == $Queue.FK_RingStrategy}selected="selected"{/if} value="{$Strategy.PK_RingStrategy}">{$Strategy.Description}</option>
			{/foreach}
			</select>
		</td>
	</tr>

	<!-- Dialed from an IVR  -->
	<tr>
		<td colspan="2">
			<input type="checkbox" name="IVRDial" id="IVRDial" value="1" {if $Queue.IVRDial=='1'}checked="checked"{/if} />
			<label for="IVRDial">This extension can be dialed from an IVR.</label>
		</td>
	</tr>
</table>



<!-- Call Queue Member Settings -->
<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td colspan="5" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/2.gif"/></td>
				<td>
					Call Queue Member Settings
				</td>
			</tr>
		</table>
	</td>
	</tr>


	<!-- Seconds to Ring Each Member -->
	<tr>
		<td>
			Seconds to Ring Each Member
		</td>
		<td colspan="4">
			<input type="text" size="5" name="MemberRingTime" value="{$Queue.MemberRingTime}" {if $Errors.MemberRingTime}class="error"{/if} />
			<br /><small>1 ring approximateley equals 4 seconds</small>
		</td>
	</tr>

	<!-- Seconds to Wait Between Members -->
	<tr>
		<td>
			Seconds to Wait Between Members
		</td>
		<td colspan="4">
			<input type="text" size="5" name="NextWaitTime" value="{$Queue.NextWaitTime}" {if $Errors.NextWaitTime}class="error"{/if} />
		</td>
	</tr>

	<!-- Seconds for Wrap Up -->
	<tr>
		<td>
			Seconds for Wrap Up
		</td>
		<td colspan="4">
			<input type="text" size="5" name="WrapUpTime" value="{$Queue.WrapUpTime}" {if $Errors.WrapUpTime}class="error"{/if} />
		</td>
	</tr>

	<!-- Pickup Announcement -->
	<tr>
		<td>
			Pickup Announcement 
		</td>
		<td colspan="4">
			<select name="FK_Sound_PickupAnnouncement">
			{html_options options=$SoundFiles selected=$Queue.FK_Sound_PickupAnnouncement}
			</select>
		</td>
	</tr>
	
		
	
	<!-- Acknowledge Call -->
	<tr>
		<td>
			Acknowledge Call
		</td>
		<td colspan="4">
			<input type="radio" value="1" id="AckCall" 
			       name="AckCall" {if $Queue.AckCall} checked="checked"{/if} /> Yes&nbsp;
			<input type="radio" value="0" id="AckCall" 
				   name="AckCall" {if !$Queue.AckCall}checked="checked"{/if}  /> No
		</td>
	</tr>
	
	<!-- Number of Missed Calls Before Auto Log Off -->
	<tr>
		<td>
			Number of Missed Calls Before Auto Log Off
		</td>
		<td colspan="4">
			<input type="text" size="5" name="MissedCallsAllowed" value="{$Queue.MissedCallsAllowed}" 
			{if $Errors.MissedCallsAllowed} class="error"{/if} />	
		</td>
	</tr>


	
	<!-- Manage Queue Memebers -->
	<tr>
		<td>
			Manage Queue Memebers
		</td>
		<td>
			<small>All Available Accounts</small>
			<br />
			<select name="AvailableMembers" id="AvailableMembers" multiple="multiple" style="width: 190px; height: 200px;">
				{foreach from=$Members item=Member}

				{assign var='print' value='1'}
				{foreach from=$Queue.Members item=Agent}
					{if $Agent == "1~"|cat:$Member.PK_Extension || $Agent == "0~"|cat:$Member.PK_Extension }
						{assign var='print' value='0'}
					{/if}
				{/foreach}

				{if $print == "1"}
						<option value="{$Member.PK_Extension}">{$Member.Extension} "{$Member.FirstName} {$Member.LastName}"</option>
				{/if}
				{/foreach}
			</select>
		</td>
		<td style="vertical-align: middle;">
			<a href="javascript: AddLoginMember()">
			<img src="../static/images/right-arrow-blue.gif" alt="<<" />
			</a>
			<br />
			<a href="javascript: RemoveMember()">
			<img src="../static/images/left-arrow.gif" alt=">>" />
			</a>
			<br />
			<a href="javascript: AddPermanentMember()">
			<img src="../static/images/right-arrow-yellow.gif" alt="<<" />
			</a>
		</td>
		<td>
			<small>Call Queue Members</small>
			<br />
			<select name="Members[]" id="Members" multiple="multiple" style="width: 190px; height: 200px;">
				{foreach from=$Queue.Members item=Agent}
					{foreach from=$Members item=Member}
						{if $Agent == "1~"|cat:$Member.PK_Extension}
							<option class="nonperm" value="1~{$Member.PK_Extension}">{$Member.Extension} "{$Member.FirstName} {$Member.LastName}"</option>
						{elseif $Agent == "0~"|cat:$Member.PK_Extension}
							<option class="perm" value="0~{$Member.PK_Extension}">{$Member.Extension} "{$Member.FirstName} {$Member.LastName}"</option>
						{/if}
					{/foreach}
				{/foreach}
			</select>
		</td>
		<td style="vertical-align: middle;">
			<a href="javascript: MemberTop()">
			<img src="../static/images/triangle-top.gif" alt="<<" />
			</a>
			<br />
			<a href="javascript: MemberUp()">
			<img src="../static/images/triangle-up.gif" alt="<<" />
			</a>
			<br />
			<a href="javascript: MemberDown()">
			<img src="../static/images/triangle-down.gif" alt="<<" />
			</a>
			<br />
			<a href="javascript: MemberBottom()">
			<img src="../static/images/triangle-bottom.gif" alt=">>" />
			</a>
		</td>
	</tr>
	<tr>
		<td colspan="3"></td>
		<td>
			<div class="nonperm" style="border: 1px solid rgb(0, 0, 0); height: 10px; width: 10px; float: left"></div> <small>&nbsp;Denotes Member Must Login</small>
			<div style="float: clear"></div>
			<div class="perm" style="border: 1px solid rgb(0, 0, 0); height: 10px; width: 10px; float: left"></div>    <small>&nbsp;Denotes Permanent Member</small>
		</td>
		<td>
		</td>
	</tr>

	<!-- Queue members can receive calls while already on a call. -->
	<tr>
		<td colspan="5">
			<input type="checkbox" name="RingInUse" id="RingInUse" value="yes" {if $Queue.RingInUse=='yes'}checked{/if} />
			<label for="RingInUse">Queue members can receive calls while already on a call.</label>
		</td>
	</tr>
</table>


<!-- Caller Experience Settings -->
<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/3.gif"/></td>
				<td>
					Caller Experience Settings
				</td>
			</tr>
		</table>
	</td>

	<!-- Play Ringing in Queue -->
	<tr>
		<td colspan="2">
			<input type="radio" name="PlayMohInQueue" value="0" {if $Queue.PlayMohInQueue=='0'}checked="checked"{/if} id="PlayMohInQueue_0" onchange="MusicInQueueOptions();" />
			<label for="PlayMohInQueue_0">Play Ringing in Queue</label>
		</td>
	</tr>

	<!-- Play Music on Hold in Queue -->
	<tr>
		<td>
			<input type="radio" name="PlayMohInQueue" value="1" {if $Queue.PlayMohInQueue=='1'}checked="checked"{/if} id="PlayMohInQueue_1" onchange="MusicInQueueOptions();"/>
			<label for="PlayMohInQueue_1">Play Music on Hold in Queue</label>
		</td>
		<td class="moh">
			<select name="FK_MohGroup" id="FK_MohGroup">
				{foreach from=$MohGroups item=Group}
					<option value="{$Group.PK_Group}" {if $Queue.FK_MohGroup==$Group.PK_Group} selected="selected"{/if}>{$Group.Name}</option>
				{/foreach}
			</select>
		</td>
	</tr>

	<!-- Announce Position in Queue -->
	<tr class="moh">
		<td>
			Announce Position in Queue
		</td>
		<td>
			<select name="AnnouncePosition">
				<option {if $Queue.AnnouncePosition=='yes'}selected="selected"{/if} value="yes">Yes</option>
				<option {if $Queue.AnnouncePosition=='no' }selected="selected"{/if} value="no">No</option>
			</select>
		</td>
	</tr>

	<!-- Announce Position in Queue -->
	<tr class="moh">
		<td>
			Announce Estimated Hold Time<br />in Position Announcements.
		</td>
		<td>
			<select name="AnnounceHoldtime">
				<option {if $Queue.AnnounceHoldtime=='yes'}selected="selected"{/if} value="yes">Yes</option>
				<option {if $Queue.AnnounceHoldtime=='no' }selected="selected"{/if} value="no">No</option>
			</select>
		</td>
	</tr>

	<!-- Announcement Frequency -->
	<tr class="moh">
		<td>
			Announcement Frequency<br />
			<small>Seconds between announcements</small>
		</td>
		<td>
			<input type="text" name="AnnounceFrequency" value="{$Queue.AnnounceFrequency}" />
		</td>
	</tr>

	<!-- Manage Announcements -->
	<tr class="moh">
		<td>
			Manage Announcements
		</td>
		<td>
			<!-- You are next -->
			<table class="queue_sound">
				<tr><td>Announcement</td>
					<td>You are next</td>
				</tr>
				<tr><td>Sound File</td>
					<td>
						<select name="FK_Sound_YouAreNext">
						{html_options options=$SoundFiles selected=$Queue.FK_Sound_YouAreNext}
						</select>
					</td>
				</tr>
				<tr><td>Description</td>
					<td>Your call is now the first in line and will be answered by the <br />next available representative.</td>
				</tr>
			</table>
			<hr />
			<!-- Caller Number -->
			<table class="queue_sound">
				<tr><td>Announcement</td>
					<td>Caller Number</td>
				</tr>
				<tr><td>Sound File</td>
					<td>
						<select name="FK_Sound_ThereAre">
						{html_options options=$SoundFiles selected=$Queue.FK_Sound_ThereAre}
						</select>
					</td>
				</tr>
				<tr><td>Description</td>
					<td>You are currently caller number</td>
				</tr>
			</table>
			<hr />
			<!-- Calls Waiting -->
			<table class="queue_sound">
				<tr><td>Announcement</td>
					<td>Calls Waiting</td>
				</tr>
				<tr><td>Sound File</td>
					<td>
						<select name="FK_Sound_CallsWaiting">
						{html_options options=$SoundFiles selected=$Queue.FK_Sound_CallsWaiting}
						</select>
					</td>
				</tr>
				<tr><td>Description</td>
					<td>Waiting to speak to a representative</td>
				</tr>
			</table>
			<hr />
			<!-- Hold Time -->
			<table class="queue_sound">
				<tr><td>Announcement</td>
					<td>Hold Time</td>
				</tr>
				<tr><td>Sound File</td>
					<td>
						<select name="FK_Sound_HoldTime">
						{html_options options=$SoundFiles selected=$Queue.FK_Sound_HoldTime}
						</select>
					</td>
				</tr>
				<tr><td>Description</td>
					<td>The estimated hold time is currently</td>
				</tr>
			</table>
			<hr />
			<!-- Minutes -->
			<table class="queue_sound">
				<tr><td>Announcement</td>
					<td>Minutes</td>
				</tr>
				<tr><td>Sound File</td>
					<td>
						<select name="FK_Sound_Minutes">
						{html_options options=$SoundFiles selected=$Queue.FK_Sound_Minutes}
						</select>
					</td>
				</tr>
				<tr><td>Description</td>
					<td>Minutes</td>
				</tr>
			</table>
			<hr />
			<!-- Thank You -->
			<table class="queue_sound">
				<tr><td>Announcement</td>
					<td>Thank You</td>
				</tr>
				<tr><td>Sound File</td>
					<td>
						<select name="FK_Sound_ThankYou">
						{html_options options=$SoundFiles selected=$Queue.FK_Sound_ThankYou}
						</select>
					</td>
				</tr>
				<tr><td>Description</td>
					<td>Thank you for your patience.</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<!-- In Queue Call Routing -->
<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td class="caption">
		<table>
			<tr>
				<td><img src="../static/images/4.gif"/></td>
				<td>
					In Queue Call Routing
				</td>
			</tr>
		</table>
	</td>
	</tr>

	<tr>
		<td>
			If the caller has been waiting in the queue for
			<input type="text" name="Timeout" value="{if $Queue.Timeout!=0}{$Queue.Timeout}{/if}" size="3" />
			seconds, route the caller to extension
			<input type="text" name="TimeoutExtension" id="TimeoutExtension" value="{$Queue.TimeoutExtension}" size="3" />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=TimeoutExtension','Select Extension',415,400);">&nbsp;</button>
			<br />
			<small>Leave this blank to leave callers in the queue indefiniteley.</small>
		</td>
	</tr>

	<tr>
		<td>
			If there aren't any members logged into the queue, route all incoming queue calls to extension
			<input type="text" name="JoinEmptyExtension" id="JoinEmptyExtension" value="{$Queue.JoinEmptyExtension}" size="3" />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=JoinEmptyExtension','Select Extension',415,400);">&nbsp;</button>
			<br />
			<small>Leave blank to keep callers waiting in the queue until a queue member logs in.</small>
		</td>
	</tr>

	<tr>
		<td>
			If there are
			<input type="text" name="MaxLen" value="{if $Queue.MaxLen!=0}{$Queue.MaxLen}{/if}" size="3" />
			unanswered calls in the queue, route all new incoming queue calls to extension
			<input type="text" name="MaxLenExtension" id="MaxLenExtension" value="{$Queue.MaxLenExtension}" size="3" />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=MaxLenExtension','Select Extension',415,400);">&nbsp;</button>
			<br />
			<small>Leave blank have an unlimited queue length.</small>
		</td>
	</tr>

	<tr>
		<td>
			If there queue call has been routed to every logged in queue member
			<select name="Cycles">
				<option {if $Queue.Cycles==0}selected="selected"{/if} value="0">infinite</option>
				<option {if $Queue.Cycles==1}selected="selected"{/if} value="1">1</option>
				<option {if $Queue.Cycles==2}selected="selected"{/if} value="2">2</option>
				<option {if $Queue.Cycles==3}selected="selected"{/if} value="3">3</option>
				<option {if $Queue.Cycles==4}selected="selected"{/if} value="4">4</option>
				<option {if $Queue.Cycles==5}selected="selected"{/if} value="5">5</option>
				<option {if $Queue.Cycles==6}selected="selected"{/if} value="6">6</option>
				<option {if $Queue.Cycles==7}selected="selected"{/if} value="7">7</option>
				<option {if $Queue.Cycles==8}selected="selected"{/if} value="8">8</option>
				<option {if $Queue.Cycles==9}selected="selected"{/if} value="9">9</option>
			</select>
			times without <br />
			being answered then route that call to extension
			<input type="text" name="CyclesExtension" id="CyclesExtension" value="{$Queue.CyclesExtension}" size="3" />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=CyclesExtension','Select Extension',415,400);">&nbsp;</button>
		</td>
	</tr>

	<tr>
		<td>
			While in the queue allow callers to dial 0 to be routed to extension
			<input type="text" name="OperatorExtension" id="OperatorExtension" value="{$Queue.OperatorExtension}" size="3" />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=OperatorExtension','Select Extension',415,400);">&nbsp;</button>
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	<br />
	<button type="submit" name="submit" value="save">Modify Call Queue</button>
</p>
</form>