{foreach from=$Rooms item=Room}
[bridge_]
type=bridge
;max_members=50
;record_conference=yes
;record_file=</path/to/file>
;internal_sample_rate=auto
;mixing_interval=40
;video_mode = follow_talker
;language=en
;sound_join
;sound_leave
;sound_has_joined ; The sound played before announcing someone's name has
;sound_has_left ; The sound played when announcing someone's name has
;sound_kicked ; The sound played to a user who has been kicked from the conference.
;sound_muted  ; The sound played when the mute option it toggled on.
;sound_unmuted  ; The sound played when the mute option it toggled off.
;sound_only_person ; The sound played when the user is the only person in the conference.
;sound_only_one ; The sound played to a user when there is only one other
;sound_there_are  ; The sound played when announcing how many users there
;sound_other_in_party; ; This file is used in conjunction with 'sound_there_are"
;sound_place_into_conference ; The sound played when someone is placed into the conference
;sound_wait_for_leader  ; The sound played when a user is placed into a conference that
;sound_leader_has_left  ; The sound played when the last marked user leaves the conference.
;sound_get_pin ; The sound played when prompting for a conference pin number.
;sound_invalid_pin ; The sound played when an invalid pin is entered too many times.
;sound_locked ; The sound played to a user trying to join a locked conference.
;sound_locked_now ; The sound played to an admin after toggling the conference to locked mode.
;sound_unlocked_now; The sound played to an admin after toggling the conference to unlocked mode.
;sound_error_menu ; The sound played when an invalid menu option is entered.
;sound_begin ; The sound played to the conference when the first marked user enters the conference.
 
    conf => {$Room.Number}
{/foreach}
