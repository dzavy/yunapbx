<h2>System Status</h2>

{if $Providers|@count }
    <table class="listing fullwidth">
        <caption>VOIP Providers</caption>
        <tr>
            <th>
                <a href="?P_Sort=Name">Name</a>
                {if $P_Sort == "Name"}
                    <img src="../static/images/{$P_Order}.gif" alt="{$P_Order}" />
                {/if}
            </th>
            <th>
                <a href="?P_Sort=Host">Host</a>
                {if $P_Sort == "Host"}
                    <img src="../static/images/{$P_Order}.gif" alt="{$P_Order}" />
                {/if}
            </th>
            <th>
                <a href="?P_Sort=AccountID">Account ID</a>
                {if $P_Sort == "AccountID"}
                    <img src="../static/images/{$P_Order}.gif" alt="{$P_Order}" />
                {/if}
            </th>
            <th>
                <a href="?P_Sort=CallbackExtension">Default Extension</a>
                {if $P_Sort == "CallbackExtension"}
                    <img src="../static/images/{$P_Order}.gif" alt="{$P_Order}" />
                {/if}
            </th>
            <th>
                <a href="?P_Sort=Latency">Latency</a>
                {if $P_Sort == "Latency"}
                    <img src="../static/images/{$P_Order}.gif" alt="{$P_Order}" />
                {/if}
            </th>
            <th>
                <a href="?P_Sort=Status">State</a>
                {if $P_Sort == "Status"}
                    <img src="../static/images/{$P_Order}.gif" alt="{$P_Order}" />
                {/if}
            </th>
        </tr>

        {foreach from=$Providers item=Provider}
            <tr class="{cycle values="odd,even"}{if $Provider.Status == "Registered"}green{elseif $Provider.Status == "Unregistered"||$Provider.Status == "Rejected"||$Provider.Status=='Unreachable'}red{elseif $Provider.Status == ""}disabled{else}yellow{/if}">
                <td>{$Provider.Name}</td>
                <td>{$Provider.Host}</td>
                <td>{$Provider.AccountID}</td>
                <td>{$Provider.CallbackExtension}</td>
                <td>
                    {if $Provider.Latency != "" }
                        {$Provider.Latency} ms
                    {else}
                        n/a
                    {/if}
                </td>
                <td>
                    {if $Provider.Status == "Registered"}
                        <img src="../static/images/success.gif" alt="R" /> Registered
                    {elseif $Provider.Status == "Unregistered"}
                        <img src="../static/images/red_alert.gif" alt="!" /> Unregistered
                    {elseif $Provider.Status == "Rejected"}
                        <img src="../static/images/red_alert.gif" alt="!" /> Rejected
                    {elseif $Provider.Status == "Unreachable"}
                        <img src="../static/images/red_alert.gif" alt="!" /> Unreachable
                    {elseif $Provider.Status == ""}
                        <img src="../static/images/not_tested.gif" alt="!" /> Unmonitored
                    {else}
                        <img src="../static/images/alert.gif" alt="!" /> {$Provider.Status}
                    {/if}
                </td>
            </tr>
        {/foreach}
    </table>
{else}
    <p class="warning_message">
        There are no VoIP providers defined on this system. 
        Use the <em>System / VoIP Providers</em> page to define one.
    </p>
{/if}

<br/>

{if $Dongles|@count }
    <table class="listing fullwidth">
        <caption>3G Dongles</caption>
        <tr>
            <th>
                <a href="?D_Sort=Name">Name</a>
                {if $D_Sort == "Name"}
                    <img src="../static/images/{$D_Order}.gif" alt="{$D_Order}" />
                {/if}
            </th>
            <th>
                <a href="?D_Sort=IMEI">IMEI</a>
                {if $D_Sort == "IMEI"}
                    <img src="../static/images/{$D_Order}.gif" alt="{$D_Order}" />
                {/if}
            </th>
            <th>
                <a href="?D_Sort=IMSI">IMSI</a>
                {if $D_Sort == "IMSI"}
                    <img src="../static/images/{$D_Order}.gif" alt="{$D_Order}" />
                {/if}
            </th>
            <th>
                <a href="?D_Sort=RSSI">RSSI</a>
                {if $D_Sort == "RSSI"}
                    <img src="../static/images/{$D_Order}.gif" alt="{$D_Order}" />
                {/if}
            </th>
            <th>
                <a href="?D_Sort=Provider">Provider</a>
                {if $D_Sort == "Provider"}
                    <img src="../static/images/{$D_Order}.gif" alt="{$D_Order}" />
                {/if}
            </th>
            <th>
                <a href="?D_Sort=Mode">Mode</a>
                {if $D_Sort == "Mode"}
                    <img src="../static/images/{$D_Order}.gif" alt="{$D_Order}" />
                {/if}
            </th>
            <th>
                <a href="?D_Sort=Status">State</a>
                {if $D_Sort == "Status"}
                    <img src="../static/images/{$D_Order}.gif" alt="{$D_Order}" />
                {/if}
            </th>
        </tr>

        {foreach from=$Dongles item=Dongle}
            <tr class="{cycle values="odd,even"}{if $Dongle.Provider == "NONE"}red{elseif $Dongle.Provider == "Unknown"}red{else}green{/if}">
                <td>{$Dongle.Name}</td>
                <td>{$Dongle.IMEI}</td>
                <td>{$Dongle.IMSI}</td>
                <td>{$Dongle.RSSI}</td>
                <td>{$Dongle.Provider}</td>
                <td>{$Dongle.Mode}</td>
                <td>{$Dongle.Status}</td>
            </tr>
        {/foreach}
    </table>
{else}
    <p class="warning_message">
        There are no 3G dongles defined on this system. 
        Use the <em>System / VoIP Providers</em> page to define one.
    </p>
{/if}

<br/>

{if $Extensions|@count}
    <table class="listing fullwidth">
        <caption>SIP Phones ( {$C_Start+1} to {$C_End} ) of {$C_Total}</caption>
        <tr>
            <th>
                <a href="?C_Sort=Extension">Extension</a>
                {if $C_Sort == "Extension"}
                    <img src="../static/images/{$C_Order}.gif" alt="{$C_Order}" />
                {/if}
            </th>
            <th>
                <a href="?C_Sort=Name">Caller ID</a>
                {if $C_Sort == "Name"}
                    <img src="../static/images/{$C_Order}.gif" alt="{$C_Order}" />
                {/if}
            </th>
            <th>
                <a href="?C_Sort=UserAgent">User Agent</a>
                {if $C_Sort == "UserAgent"}
                    <img src="../static/images/{$C_Order}.gif" alt="{$C_Order}" />
                {/if}
            </th>
            <th>
                <a href="?C_Sort=IPAddress">IP Address</a>
                {if $C_Sort == "IPAddress"}
                    <img src="../static/images/{$C_Order}.gif" alt="{$C_Order}" />
                {/if}
            </th>
            <th>
                <a href="?C_Sort=Status">State</a>
                {if $C_Sort == "Status"}
                    <img src="../static/images/{$C_Order}.gif" alt="{$C_Order}" />
                {/if}
            </th>
        </tr>
        {foreach from=$Extensions item=Extension}
            <tr class="{cycle values="odd,even"}{if $Extension.Status == "OK"}green{else}red{/if}">
                <td>{$Extension.Extension}</td>
                <td>{$Extension.Name}</td>
                <td>{$Extension.UserAgent}</td>
                <td>
                    {if $Extension.IPAddress != ""}
                        <a href="http://{$Extension.IPAddress}/">{$Extension.IPAddress}</a>
                    {/if}
                </td>
                <td>
                    {if $Extension.Status == "OK"}
                        <img src="../static/images/success.gif" alt="OK" /> Registered
                    {/if}
                    {if $Extension.Status == "UNREACHABLE"}
                        <img src="../static/images/red_alert.gif" alt="!" /> Unreachable
                    {/if}
                    {if $Extension.Status == "TIMEOUT"}
                        <img src="../static/images/red_alert.gif" alt="!" /> Timeout
                    {/if}
                </td>
            </tr>
        {/foreach}
    </table>
{else}
    <p class="warning_message">
        There are no SIP Phones defined on this system. 
        Use the <em>Extensions / Manage Extensions</em> page to define one.
    </p>
{/if}
