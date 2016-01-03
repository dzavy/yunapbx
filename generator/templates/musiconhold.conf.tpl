{foreach from=$Groups item=Group}
{if $Group.Name == 'default'}
[default]
mode=custom
directory={$Group.Folder}

application=/usr/bin/madplay -Qzr -o raw:- --mono -R 8000 -a {$Group.Gain}
{/if}
{/foreach}

{foreach from=$Groups item=Group}
[group_{$Group.PK_Group}]
mode=custom
directory={$Group.Folder}
application=/usr/bin/madplay -Qzr -o raw:- --mono -R 8000 -a {$Group.Gain}
{if !$Group.Ordered}
random=yes
{/if}

{/foreach}