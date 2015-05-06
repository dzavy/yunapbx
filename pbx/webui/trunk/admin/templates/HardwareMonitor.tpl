<script type="text/javascript" src="../script/jquery.selectboxes.js"></script>
{literal}
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
	<!--   lspci - <small>(Displays all PCI devices )  -->
	<tr class="advanced1 head">
		<td><strong>lsusb</strong> - <small>(Displays all USB devices )</small></td>
	</tr>
	<tr class="advanced1">
		<td>
			<pre class="pad">{$Output.lsusb}</pre>
		</td>
	</tr>

	<!--  /proc/interrupts - <small>(Displays which external devices are connected to which interrupt-lines of the CPU )   -->
	<tr class="advanced1 head">
		<td><strong>/proc/interrupts</strong> - <small>(Displays which external devices are connected to which interrupt-lines of the CPU )</small></td>
	</tr>
	<tr class="advanced1">
		<td>
			<pre class="pad">{$Output.interrupts}</pre>
		</td>
	</tr>


	<!-- free - <small>(Displays information about free and used memory on the system )-->
	<tr class="advanced1 head">
		<td><strong>free</strong> - <small>(Displays information about free and used memory on the system )</small></td>
	</tr>
	<tr class="advanced1">
		<td>
			<pre class="pad">{$Output.free}</pre>
		</td>
	</tr>

	<!--  Df - <small>(Displays filesystem disk space usage ) -->
	<tr class="advanced1 head">
		<td><strong>df</strong> - <small>(Displays filesystem disk space usage )</small></td>
	</tr>
	<tr class="advanced1">
		<td>
			<pre class="pad">{$Output.df}</pre>
		</td>
	</tr>

	<!-- ifconfig - <small>(Displays network interface configuration ) -->
	<tr class="advanced1 head">
		<td><strong>ifconfig</strong> - <small>(Displays network interface configuration )</small></td>
	</tr>

	<tr class="advanced1">
		<td>
			<pre class="pad" >{$Output.ifconfig}</pre>
		</td>
	</tr>

	<!-- arp  - <small>(Displays system arp cache.) -->
	<tr class="advanced1 head">
		<td><strong>arp</strong> - <small>(Displays system arp cache. )</small></td>
	</tr>
	<tr class="advanced1">
		<td>
			<pre class="pad">{$Output.arp}</pre>
		</td>
	</tr>

	<!-- route  - <small>(Displays the IP routing table )  -->
	<tr class="advanced1 head">
		<td><strong>route</strong> - <small>(Displays the IP routing table )</small></td>
	</tr>
	<tr class="advanced1">
		<td>
			<pre class="pad">{$Output.route}</pre>
		</td>
	</tr>

	<!-- resolve.conf  - <small>(Displays DNS servers )  -->
	<tr class="advanced1 head">
		<td><strong>resolv.conf</strong> - <small>(Displays DNS servers )</small></td>
	</tr>
	<tr class="advanced1">
		<td>
			<pre class="pad">{$Output.resolv_conf}</pre>
		</td>
	</tr>

	<!-- Uptime  - <small>(Displays how long the system has been running ) -->
	<tr class="advanced1 head">
		<td><strong>uptime</strong> - <small>(Displays how long the system has been running )</small></td>
	</tr>
	<tr class="advanced1">
		<td>
			<pre class="pad">{$Output.uptime}</pre>
		</td>
	</tr>
</table>

