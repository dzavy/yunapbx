<script type="text/javascript" src="../lib/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../lib/jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="../lib/jscalendar/lang/calendar-en.js"></script>

<h2>Time Frames</h2>
{if $Errors.StartDate.Invalid}
    <p class="error_message">Invalid date. Date format is (mm/dd/yyyy).</p>
{/if}
{if $Errors.EndDate.Invalid}
    <p class="error_message">Invalid date. Date format is (mm/dd/yyyy).</p>
{/if}
{if $Errors.StartTime.Invalid}
    <p class="error_message">Invalid start time format (H:MM)</p>
{/if}
{if $Errors.EndTime.Invalid}
    <p class="error_message">Invalid start time format (H:MM)</p>
{/if}
{if $Errors.EndTime.Missing || $Errors.StartTime.Missing}
    <p class="error_message">Must supply both End Time and Start Time.</p>
{/if}
{if $Errors.EndDay.Missing || $Errors.StartDay.Missing}
    <p class="error_message">Must supply both End Day and Start Day.</p>
{/if}
{if $Errors.EndDate.Missing || $Errors.StartDate.Missing}
    <p class="error_message">Must supply both End Date and Start Date.</p>
{/if}

{if $Message == "DELETE_INTERVAL"}
    <p class="success_message">Successfully deleted the time range from this Time Frame.</p>
{/if}
{if $Message == "ADD_INTERVAL"}
    <p class="success_message"> Successfully added a time range to this Time Frame.</p>
{/if}
{if $Message == "CREATE_TIMEFRAME"}
    <p class="success_message">Successfully created Time Frame.</p>
{/if}

<p>
    <a href="TimeFrames.php">
        <img src="images/left-arrow.gif" alt="<" />	Back to Time Frames
    </a>
</p>

<br /><hr /><br />

<p>
    A time frame is considered valid when it matches <strong>any</strong> of the time ranges. It does not have to match <strong>all</strong> of them. 
</p>

<br />

{if ! $ReadOnly}
    <h3>Manage this Time Frame</h3>
    <p>
        <a href="#" class="help">How does this work?</a>
    </p>
{else}
    <h3>Time Frame Definition</h3>
{/if}

<table class="listing fullwidth">
    <tr>
        <th><strong>Start Date</strong></th>
        <th><strong>End Date</strong></th>
        <th><strong>Start Day</strong></th>
        <th><strong>End Day</strong></th>
        <th><strong>Start Time</strong></th>
        <th><strong>End Time</strong></th>
            {if ! $ReadOnly}
            <th><strong>Action</strong></th>
            {/if}
    </tr>

    {foreach from=$Intervals item=I}
        <tr class="{cycle values="even,odd"}">
            <td>
                {if $I.StartDate != "0000/00/00"}
                    {$I.StartDate}
                {else}
                    -
                {/if}
            </td>
            <td>
                {if $I.EndDate != "0000/00/00"}
                    {$I.EndDate}
                {else}
                    -
                {/if}
            </td>
            <td>
                {if $I.StartDay != "0"}
                    {if $I.StartDay == "1"}Monday{/if}
                    {if $I.StartDay == "2"}Tuesday{/if}
                    {if $I.StartDay == "3"}Wednesday{/if}
                    {if $I.StartDay == "4"}Thursday{/if}
                    {if $I.StartDay == "5"}Friday{/if}
                    {if $I.StartDay == "6"}Saturday{/if}
                    {if $I.StartDay == "7"}Sunday{/if}
                {else}
                    -
                {/if}
            </td>
            <td>
                {if $I.EndDay != "0"}
                    {if $I.EndDay == "1"}Monday{/if}
                    {if $I.EndDay == "2"}Tuesday{/if}
                    {if $I.EndDay == "3"}Wednesday{/if}
                    {if $I.EndDay == "4"}Thursday{/if}
                    {if $I.EndDay == "5"}Friday{/if}
                    {if $I.EndDay == "6"}Saturday{/if}
                    {if $I.EndDay == "7"}Sunday{/if}
                {else}
                    -
                {/if}
            </td>
            <td>
                {if $I.StartTime != ""}
                    {$I.StartTime} {$I.StartTimeMode|lower}
                {else}
                    -
                {/if}
            </td>
            <td>
                {if $I.EndTime != ""}
                    {$I.EndTime} {$I.EndTimeMode|lower}
                {else}
                    -
                {/if}
            </td>
            {if ! $ReadOnly}
                <td>
                    <form action="TimeFrames_Modify.php?FK_Timeframe={$Interval.FK_Timeframe}" method="post">
                        <input type="hidden" name="FK_Timeframe" value="{$Interval.FK_Timeframe}" />
                        <input type="hidden" name="PK_Interval"  value="{$I.PK_Interval}" />
                        <button class="important" name="del" type="submit">Delete</button>
                    </form>
                </td>
            {/if}
        </tr>
    {/foreach}

    {if ! $ReadOnly}
        <tr>
        <form action="TimeFrames_Modify.php?FK_Timeframe={$Interval.FK_Timeframe}" method="post">
            <input type="hidden" name="FK_Timeframe" value="{$Interval.FK_Timeframe}" />
            <td>
                {if $Errors.StartDate}
                    {dhtml_calendar name="StartDate" style="width: 80px" value=$Interval.StartDate class="error"}
                {else}
                    {dhtml_calendar name="StartDate" style="width: 80px" value=$Interval.StartDate }
                {/if}
            </td>
            <td>
                {if $Errors.EndDate}
                    {dhtml_calendar name="EndDate" style="width: 80px" value=$Interval.EndDate class="error"}
                {else}
                    {dhtml_calendar name="EndDate" style="width: 80px" value=$Interval.EndDate }
                {/if}
            </td>
            <td>
                <select name="StartDay">
                    <option value="">-</option>
                    <option {if $Interval.StartDay=="1"}selected="selected"{/if} value="1">Monday</option>
                    <option {if $Interval.StartDay=="2"}selected="selected"{/if} value="2">Tuesday</option>
                    <option {if $Interval.StartDay=="3"}selected="selected"{/if} value="3">Wednesday</option>
                    <option {if $Interval.StartDay=="4"}selected="selected"{/if} value="4">Thursday</option>
                    <option {if $Interval.StartDay=="5"}selected="selected"{/if} value="5">Friday</option>
                    <option {if $Interval.StartDay=="6"}selected="selected"{/if} value="6">Saturday</option>
                    <option {if $Interval.StartDay=="7"}selected="selected"{/if} value="7">Sunday</option>
                </select>
            </td>
            <td>
                <select name="EndDay">
                    <option value="">-</option>
                    <option {if $Interval.EndDay=="1"}selected="selected"{/if} value="1">Monday</option>
                    <option {if $Interval.EndDay=="2"}selected="selected"{/if} value="2">Tuesday</option>
                    <option {if $Interval.EndDay=="3"}selected="selected"{/if} value="3">Wednesday</option>
                    <option {if $Interval.EndDay=="4"}selected="selected"{/if} value="4">Thursday</option>
                    <option {if $Interval.EndDay=="5"}selected="selected"{/if} value="5">Friday</option>
                    <option {if $Interval.EndDay=="6"}selected="selected"{/if} value="6">Saturday</option>
                    <option {if $Interval.EndDay=="7"}selected="selected"{/if} value="7">Sunday</option>
                </select>
            </td>
            <td>
                <input type="text" name="StartTime" style="width: 40px" value="{$Interval.StartTime}" {if $Errors.StartTime}class="error"{/if}/>
                <select name="StartTimeMode">
                    <option {if $Interval.StartTimeMode=="AM"}selected="selected"{/if} value="AM">AM</option>
                    <option {if $Interval.StartTimeMode=="PM"}selected="selected"{/if} value="PM">PM</option>
                </select>
            </td>
            <td>
                <input type="text" name="EndTime" style="width: 40px" value="{$Interval.EndTime}" {if $Errors.EndTime}class="error"{/if}/>
                <select name="EndTimeMode">
                    <option {if $Interval.EndTimeMode=="AM"}selected="selected"{/if} value="AM">AM</option>
                    <option {if $Interval.EndTimeMode=="PM"}selected="selected"{/if} value="PM">PM</option>
                </select>
            </td>
            <td>
                <button type="submit" name="add">Add</button>
            </td>
        </form>
    </tr>
{/if}
</table>
<br />
<small style="color: #777;">
    Midnight = 12:00 AM <br />
    Noon = 12:00 PM
</small>
