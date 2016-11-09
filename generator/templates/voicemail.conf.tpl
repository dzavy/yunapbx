{if $Settings.Voicemail_OperatorExtension != ""}
operator = yes
{/if}
{if $Settings.Voicemail_From != ""}
serveremail = {$Settings.Voicemail_From}
{/if}
{literal}
emailbody = ~~VARSTART~~\nVM_NAME~~${VM_NAME}\nVM_DUR~~${VM_DUR}\nVM_MSGNUM~~${VM_MSGNUM}\nVM_MAILBOX~~${VM_MAILBOX}\nVM_CALLERID~~${VM_CALLERID}\nVM_DATE~~${VM_DATE}\n~~VAREND~~\n
{/literal}
mailcmd = /home/rgavril/Work/TeleSoftPBX2/agi/Voicemail_SendEmail.php {$Settings.Voicemail_From}
externnotify=chmod -R a+rw /var/spool/asterisk/voicemail/default/

[default]
{foreach from=$Extensions item=Extension}
{if 'voicemail'|in_array:$Extension.Features}
{$Extension.Extension} = {$Extension.Password},{$Extension.Name},{$Extension.Email},,attach={if 'voicemail_forwarding'|in_array:$Extension.Features}yes{else}no{/if}|saycid=no|envelope=no|delete=no
{/if}
{/foreach}
