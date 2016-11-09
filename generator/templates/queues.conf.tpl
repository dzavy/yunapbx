{foreach from=$Queues item=Queue}
;{$Queue.Name}
[queue{$Queue.PK_Extension}]
musicclass = group_{$Queue.FK_MohGroup}
announce = {$Queue.Sound_PickupAnnouncement}
strategy = {$Queue.RingStrategy}
ringinuse = {$Queue.RingInUse}
wrapuptime = {$Queue.WrapUpTime}
timeout = {$Queue.Timeout}
retry =
maxlen = {$Queue.MaxLen}
{if $Queue.AnnouncePosition == 'yes'}
announce-frequency = {$Queue.AnnounceFrequency}
announce-holdtime = {$Queue.AnnounceHoldtime}
{else}
announce-frequency = 0
{/if}
{if $Queue.JoinEmptyExtension != ""}
joinempty = no
{else}
joinempty = yes
{/if}
queue-youarenext = {$Queue.Sound_YouAreNext}
queue-thereare = {$Queue.Sound_ThereAre}
queue-callswaiting = {$Queue.Sound_CallsWaiting}
queue-minutes = {$Queue.Sound_Minutes}
queue-thankyou = {$Queue.Sound_ThankYou}
{if $Queue.OperatorExtension != ""}
context = ring-queue
{/if}

{foreach from=$Queue.Members item=Member}
{if $Member.LoginRequired == 0}member = Local/{$Member.Extension}@ext{$Member.PK_Extension}_egress,,{$Member.Name},hint:{$Member.Extension}@ext{$Member.PK_Extension}_egress
{/if}
{/foreach}
{/foreach}
