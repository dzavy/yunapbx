[general]
format=wav49|gsm|wav
serveremail=asterisk
attach=yes
skipms=3000
maxsilence=10
silencethreshold=128
maxlogins=3
emaildateformat=%A, %B %d, %Y at %r
sendvoicemail=yes ; Allow the user to compose and send a voicemail while inside
fromstring=TeleSoft PBX
{if $Settings.Voicemail_OperatorExtension != ""}
operator=yes
{/if}
{if $Settings.Voicemail_From != ""}
serveremail={$Settings.Voicemail_From}
{/if}
{literal}
emailbody=~~VARSTART~~\nVM_NAME~~${VM_NAME}\nVM_DUR~~${VM_DUR}\nVM_MSGNUM~~${VM_MSGNUM}\nVM_MAILBOX~~${VM_MAILBOX}\nVM_CALLERID~~${VM_CALLERID}\nVM_DATE~~${VM_DATE}\n~~VAREND~~\n
{/literal}
mailcmd=/home/rgavril/Work/TeleSoftPBX2/agi/Voicemail_SendEmail.php {$Settings.Voicemail_From}
externnotify=chmod -R a+rw /var/spool/asterisk/voicemail/default/


[zonemessages]
eastern=America/New_York|'vm-received' Q 'digits/at' IMp
central=America/Chicago|'vm-received' Q 'digits/at' IMp
central24=America/Chicago|'vm-received' q 'digits/at' H N 'hours'
military=Zulu|'vm-received' q 'digits/at' H N 'hours' 'phonetic/z_p'
european=Europe/Copenhagen|'vm-received' a d b 'digits/at' HM

[default]
{foreach from=$Extensions item=Extension}
{if 'voicemail'|in_array:$Extension.Features}
{$Extension.Extension} => {$Extension.Password},{$Extension.Name},{$Extension.Email},,attach={if 'voicemail_forwarding'|in_array:$Extension.Features}yes{else}no{/if}|saycid=no|envelope=no|delete=no
{/if}
{/foreach}

[other]
