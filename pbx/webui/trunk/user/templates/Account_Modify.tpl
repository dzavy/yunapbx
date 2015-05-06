<h2>Modify Account</h2>

<p>
    Update account settings for extension <strong>{$Extension.Extension}</strong>
</p>

<form action="Account_Modify.php" method="post">
    <table class="formtable">
        <!-- First Name -->
        <tr>
            <td>
                First Name<br/>
            </td>
            <td>
                {if $Extension.FirstName_Editable}
                    <input type="text" name="FirstName" value="{$Extension.FirstName}" {if $Errors.FirstName }class="error"{/if} />&nbsp;
                {else}
                    {$Extension.FirstName}
                {/if}
            </td>
        </tr>

        <!-- Last Name -->
        <tr>
            <td>Last Name</td>
            <td>
                {if $Extension.LastName_Editable}
                    <input type="text" name="LastName" value="{$Extension.LastName}" />&nbsp;
                {else}
                    {$Extension.LastName}
                {/if}
            </td>
        </tr>

        <!-- Email Address -->
        <tr>
            <td>
                Email Address<br/>
                <small>Voicemail may be sent here.</small>
            </td>
            <td>
                {if $Extension.Email_Editable}
                    <input type="text" name="Email" value="{$Extension.Email}" />&nbsp;
                {else}
                    {$Extension.Email}
                {/if}
            </td>
        </tr>

        {if $Extension.Password_Editable}
            <!-- Numeric Password -->
            <tr>
                <td>
                    Numeric Password<br/>
                    <small>For voicemail & web tool access</small>
                </td>
                <td>
                    <input type="password" name="Password" {if $Errors.Extension}class="error"{/if} />&nbsp;
                    <br />
                    <small>Leave blank to keep current password.</small>
                </td>
            </tr>

            <!-- Retype Numeric Password -->
            <tr>
                <td>
                    Retype Numeric Password<br/>
                    <small>Must match password above</small>
                </td>
                <td>
                    <input type="password" name="Password_Retype" {if $Errors.Extension}class="error"{/if} />&nbsp;
                    <br />
                    <small>Leave blank to keep current password.</small>
                </td>
            </tr>
        {/if}
    </table>

    <!-- Submit -->
    <p>
        <br />
        <button type="submit" name="submit" value="save">Modify Extension</button>
    </p>
</form>