{foreach from=$Details item=Detail}
<p style="color: #000; font-size: 10px">
<span style="color: #555">{$Detail.Date_Formated}</span> |
{if $Detail.Event == 'DIAL' }
	Dialed number ({$Detail.Data.0})
{elseif $Detail.Event == 'RING' }
	Rang {$Detail.Data.0} <{$Detail.Data.1}>

{elseif $Detail.Event == 'DIALSTATUS' }
	Received status of {$Detail.Data.0} with a cause code of {$Detail.Data.1}

{elseif $Detail.Event == 'HANGUP' }
	Call was hung up by {if $Detail.Data.1 ==""}{$Detail.Data.0}{else}{$Detail.Data.1} &lt;{$Detail.Data.0}&gt;{/if}

{elseif $Detail.Event == 'VOICEMAIL' }
	Call was sent to voicemail box of {$Detail.Data.0} <{$Detail.Data.1}>

{elseif $Detail.Event == 'INPROVIDER' }
	Received call over {$Detail.Data.0} provider ({$Detail.Data.1})

{elseif $Detail.Event == 'OUTPROVIDER' }
	Sent call over {$Detail.Data.0} provider ({$Detail.Data.1}) with number {$Detail.Data.2}

{elseif $Detail.Event == 'TRANSFER' }
	Transfered to {$Detail.Data.0} by {$Detail.Data.2} &lt;{$Detail.Data.1}&gt;

{else}
	{$Detail.Event} {$Detail.Data_CSV}
{/if}
</p>
{/foreach}