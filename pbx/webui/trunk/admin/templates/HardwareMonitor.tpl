<script type="text/javascript" src="../script/jquery.selectboxes.js"></script>
<script type="text/javascript">
{literal}
function ToggleAdvancedOptions() {
	$('.advanced1').toggleClass('hidden');
}

function redirect(val){
	location.href = 'HardwareMonitor.php?Advanced='+val;
}

$(document).ready(function(){
{/literal}
{if $Advanced == 1}
	{literal}
	ToggleAdvancedOptions();
	{/literal}
{/if}
{literal}
});
</script>

<style type="text/css">
table.hwinfo {
	width: 100%;
}

table.hwinfo td {
	padding: 5px;
}

table.hwinfo tr.head td {
	border-bottom: 1px solid #000;
	padding-top: 20px;
}

.treshold_good {
	background-color: #F5FBE1;
	border: 1px solid #7BA813;
	color: #7BA813;
	padding: 1px 3px;
}

.treshold_over {
	background-color: #fff0f0;
	border: 1px solid #990000;
	color: #990000;
	padding: 1px 3px;
}

</style>
{/literal}

<h2>Hardware Monitor</h2>

<table class="hwinfo">
	<tr class="head">
		<td><strong>memory</strong></td>
		<td style="text-align:right;">
			Current Status:
			{if $HardwareMonitor.SwapUsed < $HardwareMonitor.SwapTreshold}
				<span class="treshold_good">Good</span>
			{else}
				<span class="treshold_over"> Over Threshold!</span>
			{/if}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			Current Status Value: mem used = %{$HardwareMonitor.MemoryUsed}, swap used = %{$HardwareMonitor.SwapUsed}<br />
			Last Updated: {$HardwareMonitor.LastUpdate}<br/>
			Threshold: maximum percentage of swap memory used ( {$HardwareMonitor.SwapTreshold} ) <br/>
		</td>
	</tr>

	<tr class="head">
		<td><strong>disk</strong></td>
		<td style="text-align:right;">
			Current Status:
			{if $HardwareMonitor.ProcDiskUsed < $HardwareMonitor.DiskThreshold}
				<span class="treshold_good">Good</span>
			{else}
				<span class="treshold_over"> Over Threshold!</span>
			{/if}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			Current Status Value: Disk Total:{$HardwareMonitor.DiskTotal}  Used:{$HardwareMonitor.DiskUsed}({$HardwareMonitor.ProcDiskUsed}%)  Available:{$HardwareMonitor.DiskAvailable} <br/>
			Last Updated: {$HardwareMonitor.LastUpdate} <br/>
			Threshold: maximum percentage of disk used ( {$HardwareMonitor.DiskThreshold} ) <br/>
		</td>
	</tr>


	<tr class="head">
		<td><strong>load</strong></td>
		<td style="text-align:right;">
			Current Status:
			{if $HardwareMonitor.LoadAverage < $HardwareMonitor.LoadAverageTreshold}
				<span class="treshold_good">Good</span>
			{else}
				<span class="treshold_over"> Over Threshold!</span>
			{/if}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			Current Status Value: Load Average is {$HardwareMonitor.LoadAverage} <br/>
			Last Updated: {$HardwareMonitor.LastUpdate} <br/>
			Threshold: maximum load average ( {$HardwareMonitor.LoadAverageTreshold} ) <br/>
		</td>
	</tr>
</table>


<table class="hwinfo">
	<tr class="advanced1">
		<td>
			<br/>
				<button type="button" onclick="redirect(1);">
					Show Advanced Hardware Informations
				</button>
			<br/>
		</td>
	</tr>

	<tr class="advanced1 hidden">
		<td>
			<br/>
				<button type="button" onclick="redirect(0);">
					Hide Advanced Hardware Informations
				</button>
			<br/><br/>
		</td>
	</tr>

	<!--   lspci - <small>(Displays all PCI devices )  -->
	<tr class="advanced1 hidden head">
		<td><strong>lspci</strong> - <small>(Displays all PCI devices )</small></td>
	</tr>
	<tr class="advanced1 hidden">
		<td>
			<pre class="pad">{$Output.lspci}</pre>
		</td>
	</tr>

	<!--  /proc/interrupts - <small>(Displays which external devices are connected to which interrupt-lines of the CPU )   -->
	<tr class="advanced1 hidden head">
		<td><strong>/proc/interrupts</strong> - <small>(Displays which external devices are connected to which interrupt-lines of the CPU )</small></td>
	</tr>
	<tr class="advanced1 hidden">
		<td>
			<pre class="pad">{$Output.interrupts}</pre>
		</td>
	</tr>


	<!-- free - <small>(Displays information about free and used memory on the system )-->
	<tr class="advanced1 hidden head">
		<td><strong>free</strong> - <small>(Displays information about free and used memory on the system )</small></td>
	</tr>
	<tr class="advanced1 hidden">
		<td>
			<pre class="pad">{$Output.free}</pre>
		</td>
	</tr>

	<!--  Df - <small>(Displays filesystem disk space usage ) -->
	<tr class="advanced1 hidden head">
		<td><strong>df</strong> - <small>(Displays filesystem disk space usage )</small></td>
	</tr>
	<tr class="advanced1 hidden">
		<td>
			<pre class="pad">{$Output.df}</pre>
		</td>
	</tr>

	<!-- ifconfig - <small>(Displays network interface configuration ) -->
	<tr class="advanced1 hidden head">
		<td><strong>ifconfig</strong> - <small>(Displays network interface configuration )</small></td>
	</tr>

	<tr class="advanced1 hidden">
		<td>
			<pre class="pad" >{$Output.ifconfig}</pre>
		</td>
	</tr>

	<!-- arp  - <small>(Displays system arp cache.) -->
	<tr class="advanced1 hidden head">
		<td><strong>arp</strong> - <small>(Displays system arp cache. )</small></td>
	</tr>
	<tr class="advanced1 hidden">
		<td>
			<pre class="pad">{$Output.arp}</pre>
		</td>
	</tr>

	<!-- route  - <small>(Displays the IP routing table )  -->
	<tr class="advanced1 hidden head">
		<td><strong>route</strong> - <small>(Displays the IP routing table )</small></td>
	</tr>
	<tr class="advanced1 hidden">
		<td>
			<pre class="pad">{$Output.route}</pre>
		</td>
	</tr>

	<!-- resolve.conf  - <small>(Displays DNS servers )  -->
	<tr class="advanced1 hidden head">
		<td><strong>resolve.conf</strong> - <small>(Displays DNS servers )</small></td>
	</tr>
	<tr class="advanced1 hidden">
		<td>
			<pre class="pad">{$Output.resolv_conf}</pre>
		</td>
	</tr>

	<!-- Uptime  - <small>(Displays how long the system has been running ) -->
	<tr class="advanced1 hidden head">
		<td><strong>uptime</strong> - <small>(Displays how long the system has been running )</small></td>
	</tr>
	<tr class="advanced1 hidden">
		<td>
			<pre class="pad">{$Output.uptime}</pre>
		</td>
	</tr>
</table>

