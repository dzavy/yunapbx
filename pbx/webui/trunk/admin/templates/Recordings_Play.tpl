<div style="font-size: 10px">
		<b>From:</b>
		{if $File.CallerName != "" }
			{$File.CallerName} &lt;{$File.CallerNumber}&gt;
		{else}
			{$File.CallerNumber}
		{/if}
	<br/>
		<b>To:</b>
		{if $File.CalledName != "" }
			{$File.CalledName} &lt;{$File.CalledNumber}&gt;
		{else}
			{$File.CalledNumber}
		{/if}
	<br />
		<b>Date:</b>
		{$File.StartDate}
</div>
<br />
<div>
<!--	<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="328" height="30">
		<param name="movie" value="swf/player-viral.swf" />
		<param name="allowfullscreen" value="false" />
		<param name="allowscriptaccess" value="always" />
		<param name="flashvars" value="file=Recordings_Download.php" />
		<object type="application/x-shockwave-flash" data="swf/player-viral.swf" width="280" height="20">
			<param name="movie" value="swf/player-viral.swf" />
			<param name="allowfullscreen" value="true" />
			<param name="allowscriptaccess" value="always" />
			<param name="flashvars" value="file=Recordings_Download.php?ID={$File.PK_CallLog}&type=sound&autostart=true" />
			<p><a href="http://get.adobe.com/flashplayer">Get Flash</a> to see this player.</p>
		</object>
	</object>-->

	<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95"
        codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,0,02,902"
        width="328" height="45" type="application/x-oleobject" name="sound1">
		  <param name="filename" value="Recordings_Download.php?ID={$File.FK_CallLog}">
		  <param name="autostart" value="false">
		  <param name="showcontrols" value="true">
		  <!--[if !IE]> <-->
		    <object data="Recordings_Download.php?ID={$File.FK_CallLog}" width="328" height="45" type="application/x-mplayer2" name="sound1">
		      <param name="pluginurl" value="http://www.microsoft.com/Windows/MediaPlayer/">
		      <param name="controller" value="true">
		    </object>
		  <!--> <![endif]-->
</object>
</div>
<div>
	<a href="Recordings_Download.php?ID={$File.FK_CallLog}">Click here to download</a>
</div>