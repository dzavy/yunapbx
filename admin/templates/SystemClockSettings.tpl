<script type="text/javascript" src="../lib/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../lib/jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="../lib/jscalendar/lang/calendar-en.js"></script>

<script type="text/javascript" src="../script/jquery.checkboxes.js"></script>
<script language="javascript">
{literal}

function UpdateNTPFields(val){
    
    if (val == 0){
        $(".advanced3").removeClass('disabled');
		$("#NTPServer").attr('disabled', 'disabled');
		$("#f-calendar-field-1").removeAttr('disabled');
		$("#checkbox1").removeAttr('checked');
    }
	
	else {	
		$(".advanced3").addClass('disabled');
	   	$("#NTPServer").removeAttr('disabled');
		$("#f-calendar-field-1").attr('disabled', 'disabled');
		$("#checkbox1").attr('checked', 'checked');
    }
}

function toggleNTPFields(){
    if ($('#checkbox1:checked').val()){
		$(".advanced3").addClass('disabled');
		$("#f-calendar-field-1").attr('disabled', 'disabled');
       	$("#NTPServer").removeAttr('disabled');
		$("#DisableNTPLast").attr("value", "1");
    }
	
    else {	
        $(".advanced3").removeClass('disabled');
		$("#NTPServer").attr('disabled', 'disabled');
		$("#f-calendar-field-1").removeAttr('disabled');
		$("#checkbox1").removeAttr('checked');
		$("#DisableNTPLast").attr("value", "0");
    }
}

$(document).ready(function(){
	UpdateNTPFields({/literal}{$Settings.DisableNTP}{literal});
});
{/literal}
</script>
{*php}
print ("<pre>");
print_r ($this->_tpl_vars['Settings']);
print ("</pre>");
{/php*}



<h2>Set System Clock</h2>
{if $Message == "SAVED_SYSTEMCLOCK_SETTINGS"}
	<p class="success_message">Successfully saved system clock settings.</p>
{/if}

<form action="SystemClockSettings.php" method="post" name="SystemClock">
<table class="nostyle">
	<tr>
		<td> Your Current System Time: 	&nbsp;</td>
		<td> {$CurrentTime}<br></td>
	</tr>
	<tr>
		<td> Your Current Timezone:	</td>
		<td> <i>{$Settings.Current_TimeZone}</i></td>
	</tr>
</table>
<br />

<h2>Set your Timezone:</h2>
<table>
	<tr>
		<td>
			Timezone:
		<td>
		<select name="TimeZones[]" id="TimeZones"/>
				{foreach from=$TimeZones item=Current_TimeZone}
					<option label="" value="{$Current_TimeZone}" {if $Current_TimeZone == $Settings.Current_TimeZone} selected {/if}>{$Current_TimeZone}</option>
				{/foreach}
			</select>	
		
		</td>
	</tr>	
</table>
<br />

<h2>Set your System Date and Time:</h2>
<table class="formtable">
	<tr>
		<td>
			<div  class="advanced3" id="div_calendar" name="div_calendar" >
			Manually Set Your Date/Time: 
			{if $Errors.EndDate}
				{dhtml_calendar name="EndDate" value=$Interval.EndDate class="error" format="%Y/%m/%d %H:%M"}
			{else}
				{dhtml_calendar name="EndDate" format="%Y/%m/%d %H:%M"}
			{/if}
			</div>
			<small>Disable NTP time fetching below to enable this option.</small>
		</td>
	</tr>
	<tr>
		<td>- OR -</td>
	</tr>
	<tr>
		<td class="advanced4"> Use an NTP Server: <input type="text" id="NTPServer" name="NTPServer" value="{$Settings.NTPServer|escape:html}" {if $Errors.NTPServer} class="error" {/if}> 
			 <input type="checkbox" name="checkbox1" id="checkbox1" onclick='toggleNTPFields()' > Disable NTP time fetching<br></td>
			 <input type="hidden"   name="DisableNTPLast" id="DisableNTPLast">
	</tr>
</table>

<br />
<p class="warning_message">Changing the system time requires a software restart. All current calls in the system will be dropped.</p>
<br />
<button type="submit" name="submit" value="save_time_settings">Save Time Settings</button>

</form>
