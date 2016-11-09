{foreach from=$Groups item=Group}
{if $Group.Name == 'default'}
[default]
{else}
[group_{$Group.PK_Group}]
{/if}
mode = files
directory = {$Group.Folder}
format = alaw
{if $Group.Ordered}
sort = alpha
{else}
sort = random
{/if}

{/foreach}
