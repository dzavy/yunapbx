{if $Display == 'Name' || $Display == ''}
	{if     $Action.Type == 'play_sound'         } Play Sound
	{elseif $Action.Type == 'dial_extension'     } Dial Extension
	{elseif $Action.Type == 'send_to_voicemail'  } Send to Voicemail
	{elseif $Action.Type == 'email_rec_sound'    } Email Recorded Sound
	{elseif $Action.Type == 'alter_callerid'     } Alter Caller ID
	{elseif $Action.Type == 'time_clause'        } Time Based Clause
	{elseif $Action.Type == 'wait'               } Wait
	{elseif $Action.Type == 'change_language'    } Change Lanugage
	{elseif $Action.Type == 'playback_rec_sound' } Play Recorded Sound
	{elseif $Action.Type == 'give_busy'          } Give Busy Signal
	{elseif $Action.Type == 'hang_up'            } Hang Up
	{elseif $Action.Type == 'say_digits'         } Say Digits/Letters
	{elseif $Action.Type == 'say_number'         } Say A Number
	{elseif $Action.Type == 'set_variable'       } Set Variable
	{elseif $Action.Type == 'play_digits'        } Play DTMF Tones
	{elseif $Action.Type == 'record_digits'      } Record Digits
	{elseif $Action.Type == 'record_sound'       } Record Sound
	{elseif $Action.Type == 'gatekeeper'         } Gate Keeper
	{elseif $Action.Type == 'conditional_clause' } Conditional Clause
	{elseif $Action.Type == 'goto_context'       } Go To IVR Menu / Action
	{elseif $Action.Type != ''                   } {$Action.Type}
	{else                                        } IVR Menu Begining
	{/if}
{/if}

{if $Display == 'Desc' || $Display == ''}
	{if     $Action.Type == 'play_sound'         } {$Action.Sound.FK_SoundEntry}
	{elseif $Action.Type == 'dial_extension'     } {if $Action.Param.Extension!=""}"{$Action.Param.Extension}"{else}<em>{$Action.Var.Extension}</em>{/if}
	{elseif $Action.Type == 'send_to_voicemail'  } {if $Action.Param.Extension!=""}"{$Action.Param.Extension}"{else}<em>{$Action.Var.Extension}</em>{/if}
	{elseif $Action.Type == 'email_rec_sound'    } <em>{$Action.Var.Sound}</em> -> {$Action.Param.Email}
	{elseif $Action.Type == 'alter_callerid'     } {if $Action.Param.Method=='replace'}Replace with {/if}{if $Action.Param.Method=='append'}Append {/if}{if $Action.Param.Method=='prepend'}Prepend {/if}{if $Action.Param.Text!=""}"{$Action.Param.Text}"{else}<em>{$Action.Var.Text}</em>{/if}
	{elseif $Action.Type == 'time_clause'        }
	{elseif $Action.Type == 'wait'               } {$Action.Param.Time} seconds
	{elseif $Action.Type == 'change_language'    }
	{elseif $Action.Type == 'playback_rec_sound' } <em>{$Action.Var.Sound}</em>
	{elseif $Action.Type == 'give_busy'          }
	{elseif $Action.Type == 'hang_up'            }
	{elseif $Action.Type == 'say_digits'         } {if $Action.Param.Digits!=""}"{$Action.Param.Digits}"{else}<em>{$Action.Var.Digits}</em>{/if}
	{elseif $Action.Type == 'say_number'         } {if $Action.Param.Number!=""}"{$Action.Param.Number}"{else}<em>{$Action.Var.Number}</em>{/if}
	{elseif $Action.Type == 'set_variable'       } Set <em>{$Action.Param.Name}</em> = "{$Action.Param.Value}"
	{elseif $Action.Type == 'play_digits'        } {if $Action.Param.Digits!=""}"{$Action.Param.Digits}"{else}<em>{$Action.Var.Digits}</em>{/if}
	{elseif $Action.Type == 'record_digits'      } <em>{$Action.Param.Name}</em>
	{elseif $Action.Type == 'record_sound'       } <em>{$Action.Param.Name}</em>
	{elseif $Action.Type == 'gatekeeper'         } <em>{$Action.Param.Name}</em>
	{elseif $Action.Type == 'conditional_clause' } If <em>{$Action.Var.Value1}</em> {$Action.Param.Operator} {$Action.Param.Value2}
	{elseif $Action.Type == 'goto_context'       } {$Action.Param.PK_Menu}
	{else                                        } {$Action.Type}
	{/if}
{/if}