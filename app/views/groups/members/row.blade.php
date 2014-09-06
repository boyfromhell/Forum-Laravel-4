<tr id="member{{ $member->id }}">
	<td class="icon">
	@if ( $member->level->image )
		<img src="/images/titles/{{ $member->level->image }}" title="{{{ $member->level->name }}}">
	@else
		{{ $member->counter }}
	@endif
	</td>
	<td><a href="{{ $member->url }}">{{{ $member->name }}}</a></td>
	<td style="width:20%; text-align:center">{{ $role }}</td>
</tr>

{{-- if $membership == 2}
<a href="" class="del" onclick="deleteMember({$group->id}, {$member->id}); return false">x</a>
{/if --}}
