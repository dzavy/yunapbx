
<script language="javascript">
	{literal}		
		function Redirect(){
			
htmlStr =  "<p style='text-align:right;'>                                                                             ";
htmlStr += "<A href='javascript:self.close()'>  <b>X</b> Close Window</A> <br/><br/>                                  ";
htmlStr += "</p>                                                                                                      ";
htmlStr += "<center>                                                                                                  ";
htmlStr += "<table class='nostyle' style='width:700px;'>                                                              ";
htmlStr += "	<tr>                                                                                                  ";
htmlStr += "		<td style='border-bottom:solid #dddddd; border-bottom-width:1px; text-align:left;'>               ";
htmlStr += "			FTP Server Connectivity Test                                                                  ";
htmlStr += "		</td>                                                                                             ";
htmlStr += "		<td style='border-bottom:solid #dddddd; border-bottom-width:1px; text-align:right; width:100px;'> ";
htmlStr += "			<p class='{/literal}{$Connect.Class}{literal}'>                                               ";
htmlStr += "				{/literal}{$Connect.Status}{literal}                                                      ";
htmlStr += "			</p>							                                                              ";
htmlStr += "		</td>                                                                                             ";
htmlStr += "	</tr>                                                                   							  ";
htmlStr += "	<tr>																								  ";
htmlStr += "		<td colspan=2 style='text-align:left;'>                                                           ";
htmlStr += "            {/literal}{$Connect.Message}{literal} <br/><br/>					                          ";
htmlStr += "        </td>					                                                                          ";
htmlStr += "    </tr>																							      ";
htmlStr += "	<tr><td colspan=2><hr></td></tr>                             								          ";
htmlStr += "	<tr>																								  ";
htmlStr += "		<td style='border-bottom:solid #dddddd; border-bottom-width:1px; text-align:left;'>               ";
htmlStr += "             FTP Server Login Test   																	  ";
htmlStr += "		</td> 																							  ";
htmlStr += "        <td style='border-bottom:solid #dddddd; border-bottom-width:1px; text-align:right; width:100px;'> ";
htmlStr += "			<p class='{/literal}{$Login.Class}{literal}'>											      ";
htmlStr += "				{/literal}{$Login.Status}{literal}                                                        ";
htmlStr += "			</p> 																						  ";
htmlStr += "		</td> 																						      ";
htmlStr += "	</tr>																								  ";
htmlStr += "	<tr>																								  ";
htmlStr += "		<td colspan=2 style='text-align:left;'>                                                           ";
htmlStr += "			{/literal}{$Login.Message}{literal} <br/> <br/>                                               ";
htmlStr += "		</td>																							  ";
htmlStr += "	</tr>																								  ";
htmlStr += "	<tr><td colspan=2><hr></td></tr>																	  ";
htmlStr += "	<tr>																								  ";
htmlStr += "		<td style='border-bottom:solid #dddddd; border-bottom-width:1px; text-align:left;'>               ";
htmlStr += "			FTP Change Path Test 																		  ";
htmlStr += "		</td>																							  ";
htmlStr += "		<td style='border-bottom:solid #dddddd; border-bottom-width:1px; text-align:right; width:100px;'> ";
htmlStr += "			<p class='{/literal}{$Path.Class}{literal}'>											      ";
htmlStr += "				{/literal}{$Path.Status}{literal}                                                         ";
htmlStr += "			</p>																						  ";
htmlStr += "		</td>																						      ";
htmlStr += "	</tr>																								  ";
htmlStr += "	<tr>																								  ";
htmlStr += "		<td colspan=2 style='text-align:left;'>                                                           ";
htmlStr += "			{/literal}{$Path.Message}{literal}	<br/> <br/>                                               ";
htmlStr += "		</td>	                                                                                          ";
htmlStr += "	</tr> 																								  ";
htmlStr += "	<tr><td colspan=2><hr></td></tr>																	  ";
htmlStr += "	<tr>																								  ";
htmlStr += "		<td style='border-bottom:solid #dddddd; border-bottom-width:1px;text-align:left;'>                ";
htmlStr += "			FTP Upload File Test                                                                          ";
htmlStr += "		</td>																							  ";
htmlStr += "		<td style='border-bottom:solid #dddddd; border-bottom-width:1px; text-align:right; width:100px;'> ";
htmlStr += "			<p class='{/literal}{$Upload.Class}{literal}'>												  ";
htmlStr += "				{/literal}{$Upload.Status}{literal}  													  ";
htmlStr += "			</p>																						  ";
htmlStr += "		</td>																							  ";	
htmlStr += "	</tr>																								  ";
htmlStr += "	<tr>																								  ";
htmlStr += "		<td colspan=2 style='text-align:left;'>                                                           ";
htmlStr += "            {/literal}{$Upload.Message}{literal} <br/> <br/> 											  ";
htmlStr += "		</td>						   																	  ";
htmlStr += "	</tr>																							      ";
htmlStr += "	<tr><td colspan=2><hr></td></tr>																	  ";
htmlStr += "	<tr>                                                                                                  ";
htmlStr += "		<td style='border-bottom:solid #dddddd; border-bottom-width:1px; text-align:left;'>               ";
htmlStr += "			FTP Delete File Test 																		  ";
htmlStr += "		</td>                                                                                             ";
htmlStr += "		<td style='border-bottom:solid #dddddd; border-bottom-width:1px; text-align:right; width:100px;'> ";
htmlStr += "			<p class='{/literal}{$Delete.Class}{literal}'>                                                ";
htmlStr += "				{/literal}{$Delete.Status}{literal}                                                       ";
htmlStr += "			</p>	                                                                                      ";
htmlStr += "		</td> 																							  ";
htmlStr += "	</tr>                                                                                                 ";
htmlStr += "	<tr>                                                                                                  ";
htmlStr += "		<td colspan=2 style='text-align:left;'>                                                           ";
htmlStr += "           {/literal}{$Delete.Message}{literal} <br/> <br/>                                               ";
htmlStr += "    	</td>                             																  ";
htmlStr += "	</tr>                                                                                                 ";
htmlStr += "</table>                                                                                                  ";
htmlStr += "</center>                                                                                                 ";

			$("#all").replaceWith(htmlStr);
		}		
		$(document).ready(function(){
			setTimeout('Redirect()',500);			
		});		
	{/literal}
</script>


<div id="all">
	<p style="text-align:right;">
		<A href="javascript: self.close ()"><b>X</b> Close Window</A>  
		<br/>
		<br/>
	</p>
	<p>
		Testing your FTP settings. Please be patient and do not refresh this page.<br/>
		<img src="./images/wait.gif">
	</p>
</div>

