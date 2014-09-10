<tr>

	<td class="icon wide">
		<div class="icon forum{{ $forum->external ? '_external' : '' }}{{ $forum->is_unread ? '_unread' : '' }}" title="{{{ $forum->alt_text }}}">
	</td>

	<td class="forum" style="width:40%">
		<a href="{{ $forum->url }}">{{{ $forum->name }}}</a>

		@if ( count($forum->children) > 0 )
			<br><small>Sub-Forums: 
		
			@foreach ( $forum->children as $child )
				<img src="{{ $skin }}icons/minipost{{ $child->is_unread ? '_new' : '' }}.png" alt="{{{ $child->alt_text }}}" title="{{{ $child->alt_text }}}"> 
				<a href="{{ $child->url }}">{{{ $child->name }}}</a>&nbsp;&nbsp;
			@endforeach
		
			</small>
		@endif
	</td>

	<td class="lastpost" colspan="2">
		@if ( $forum->external )
			{{{ $forum->description }}}
		@elseif ( false && !$forum->perm_read )
			Private<br><small>&nbsp;</small>
		@elseif ( !$forum->total_posts )
			No posts<br><small>&nbsp;</small>
		@endif
	
		@if ( $forum->latest_topic->id )
			@if ( $forum->latest_topic->smiley )
				<img src="{{ $forum->latest_topic->smiley_img }}" alt="{{ $forum->latest_topic->smiley_alt }}">
			@endif
			@if ( $forum->latest_topic->has_attachments )
				<img src="{{ $skin }}icons/attachment.png" alt="att">
			@endif

			<a href="{{ $forum->latest_topic->url }}" title="{{{ $forum->latest_topic->alt_text }}}">{{{ $forum->latest_topic->short_title }}}</a><br>
	
			<div style="float:left; font-size:10pt">by <a href="{{ $forum->latest_topic->latest_post->user->url }}" title="{{{ $forum->latest_topic->latest_post->user->name }}}'s profile">{{{ $forum->latest_topic->latest_post->user->name }}}</a></div>

			<div style="text-align:right; font-size:10pt">{{{ $forum->latest_topic->latest_post->date }}}&nbsp;<a href="{{ $forum->latest_topic->latest_post->url }}"><img src="{{ $skin }}icons/latest_reply.png" alt="-&gt;" title="Go to last post"></a></div>
		@endif
	</td>

	<td class="posts">
		{{ number_format($forum->total_topics) }}
	</td>

	<td class="posts">
		{{ number_format($forum->total_posts) }}
	</td>
</tr>
