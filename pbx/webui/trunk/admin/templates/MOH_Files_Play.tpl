<div style="font-size: 10px">
	<a href="MOH_Files_Download.php?PK_File={$File.PK_File}">{$File.Filename}.{$File.Fileext}</a>
</div>
<br />
<div>
	<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="328" height="30">
		<param name="movie" value="swf/player-viral.swf" />
		<param name="allowfullscreen" value="false" />
		<param name="allowscriptaccess" value="always" />
		<param name="flashvars" value="file=MOH_Files_Download.php" />
		<object type="application/x-shockwave-flash" data="swf/player-viral.swf" width="280" height="20">
			<param name="movie" value="swf/player-viral.swf" />
			<param name="allowfullscreen" value="true" />
			<param name="allowscriptaccess" value="always" />
			<param name="flashvars" value="file=MOH_Files_Download.php?PK_File={$File.PK_File}&type=sound&autostart=true" />
			<p><a href="http://get.adobe.com/flashplayer">Get Flash</a> to see this player.</p>
		</object>
	</object>
</div>