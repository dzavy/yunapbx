[general]
bandwidth=low
jitterbuffer=no

; registrations
{foreach from=$IaxProviders item=Provider}
 {if $Provider.RegisterType=='Provider'}
;; {$Provider.Name}
register => {$Provider.AccountID}:{$Provider.Password}@{$Provider.Host}
;register => [% p.auth_name %]:[% IF p.outgoing_use_rsa %][[% private_key %]][% ELSE %][% p.outgoing_secret %][% END %]@[% p.register_host %]
 {/if}
{/foreach}

[guest]
type=user
context=unknown_provider

{foreach from=$IaxProviders item=Provider}
;{$Provider.Name}
[{$Provider.Label}]
type=friend
username={$Provider.AccountID}
secret={$Provider.IncomingPassword}
context=iax_provider_{$Provider.PK_IaxProvider}
auth={$Provider.AuthType}
transfer=no
;inkeys={$Provider.RSA_Key}
{if $Provider.RegisterType=='Client'}
host=dynamic
{else}
host={$Provider.Host}
{/if}
{if $Provider.Qualify}
qualify=yes
qualifysmoothing=yes
{/if}
{if $Provider.Jitterbuffer}
jitterbuffer=yes
{/if}
{if $Provider.Trunking}
trunk=yes
{/if}
;peercontext=
disallow=all
allow={$Provider.Codecs}

{foreach from=$Provider.OutHosts item=OutHost name=Out}
;;{$Provider.Name} outbound {$smarty.foreach.Out.index+1}
[{$Provider.Label}_out{$smarty.foreach.Out.index+1}]
type=peer
username={$Provider.AccountID}
secret={$Provider.IncomingPassword}
context=iax_provider_{$Provider.PK_IaxProvider}
host={$OutHost}
auth={$Provider.AuthType}
transfer=no
;inkeys={$Provider.RSA_Key}
{if $Provider.Qualify}
qualify=yes
qualifysmoothing=yes
{/if}
{if $Provider.Jitterbuffer}
jitterbuffer=yes
{/if}
{if $Provider.Trunking}
trunk=yes
{/if}
;peercontext=
disallow=all
allow={$Provider.Codecs}

{/foreach}

{/foreach}