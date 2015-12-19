<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension must be 2 digits only.</p>
{/if}
{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>
{/if}

{if $FC_Voicemail.PK_Extension != "" }
<a href="javascript:history.back();">
	<img src="../static/images/left-arrow.gif">
	<b>Back to Manage Extensions  </b>
</a>

{/if}

<form action="Extensions_FC_Voicemail_Modify.php" method="post" >
<input type="hidden" name="PK_Extension" value="{$FC_Voicemail.PK_Extension}" />
<table class="formtable">
	<!-- Agent Login Extension -->
	<tr>
		<td>
			This feature code will directly dial another extension's voicemail box.
		</td>
	</tr>

	<tr>
		<td>
			{if $FC_Voicemail.PK_Extension != "" }
				Extension <b>*{$FC_Voicemail.Extension}</b>
				<input type="hidden" name="Extension" value="{$FC_Voicemail.Extension}" />
			{else}
			<table>
				<tr>
					<td style="width:160px;">
						Feature Code Extension<br >
						<small>2 digits in length</small>
					</td>
					<td>
						*<input type="text" size="2" name="Extension" value="{$FC_Voicemail.Extension}" {if $Errors.Extension}class="error"{/if} />
					</td>
				</tr>
			</table>
			{/if}
		</td>
	</tr>

	<tr>
		<td>
			{if $FC_Voicemail.PK_Extension != "" }
			<p>
				This extension does not have any attributes to modify. <br>
				To change the extension number, please delete this extension and create a new one.
			</p>
			{else}
				<button type="submit" name="submit" value="save">Create Extension</button>
			{/if}
		</td>
	</tr>
</table>
</form>