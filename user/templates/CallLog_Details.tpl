{foreach from=$Details item=Detail}
<p>
<span style="color: #aaa">{$Detail.Date_Formated}</span> |
{    if $Detail.Event == 'DIAL' }
	Dialer number ({$Detail.Data.0})

{elseif $Detail.Event == 'RING' }
	Rang {$Detail.Data.0} <{$Detail.Data.1}>

{elseif $Detail.Event == 'DIALSTATUS' }
	Received status of {$Detail.Data.0}

{elseif $Detail.Event == 'HANGUP' }
	Call was hung up by {$Detail.Data.0}

{elseif $Detail.Event == 'VOICEMAIL' }
	Call was sent to voicemail box of {$Detail.Data.0} <{$Detail.Data.1}>

{else}
	{$Detail.Event} {$Detail.Data_CSV}
{/if}
</p>
{/foreach}