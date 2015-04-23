[general]

[rooms]
{foreach from=$Rooms item=Room}
conf => {$Room.Number}
{/foreach}
