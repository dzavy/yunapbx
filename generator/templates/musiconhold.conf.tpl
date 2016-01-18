{foreach from=$Groups item=Group}
{if $Group.Name == 'default'}
[default]
{else}
[group_{$Group.PK_Group}]
{/if}
mode=custom
directory={$Group.Folder}
format=alaw
{if $Group.Ordered}
application=/usr/bin/madplay -Qr -o raw:- --mono -R 8000 -a {$Group.Gain}
{else}
application=/usr/bin/madplay -Qzr -o raw:- --mono -R 8000 -a {$Group.Gain}
{/if}    
{/foreach}
