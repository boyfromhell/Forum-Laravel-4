<tr class="thread-row{{ $topic->unread ? ' unread' : '' }}" data-id="{{ $topic->id }}" data-all="1" data-read="{{ !$topic->unread }}" data-title="{{{ $topic->title }}}">

	@if ( $topic_mode == 'subscription' )
	<td class="icon">
		<input type="checkbox" name="topics[]" value="{{ $topic->id }}" tabindex="1">
	</td>
	@endif

	<td class="icon">
		<div class="icon {{{ $topic->img }}}" title="{{{ $topic->img_alt }}}"></div>
	</td>

	<td class="icon">
		@if ( $topic->smiley )
			<img src="{{{ $topic->smiley_img }}}" alt="{{{ $topic->smiley_alt }}}">
		@else
			&nbsp;
		@endif
	</td>

	<td style="width:{{ $topic_mode == 'subscription' ? '30%' : '50%' }}" class="topic">
		@if ( $topic->has_attachments )<img src="{{ $skin }}icons/attachment.png" alt="att">@endif
		
		@if ( $topic->has_poll )<b>[ Poll ]</b>@endif
	
		@if ( $topic->unread )<a href="{{ $topic->unread['url'] }}"><img src="{{ $skin }}icons/newest_reply.png" title="{{{ $topic->unread['alt'] }}}"></a> @endif
	
		{{{ $topic->prefix }}}
		<a href="{{ $topic->url }}" title="{{{ $topic->alt }}}">{{{ $topic->short_title }}}</a>
	
		@if ( $topic->pages > 1 )
			<small> ( <img src="{{ $skin }}icons/multi_page.png" alt="+"> 
	
			@for ( $page=1; $page<=$topic->pages; $page++ )
				@if ( $page < 4 || $page > $topic->pages-3 )
					<a href="{{ $topic->url }}?page={{ $page }}" title="Go to page {{ $page }}">{{ $page }}</a>
				@elseif ( $page == 4 )
					...
				@endif
			@endfor )
			</small>
		@endif
		<br>

		<a class="tiny" href="{{ $topic->author->url }}" title="{{{ $topic->author->name }}}'s profile">{{{ $topic->author->name }}}</a>
	</td>

	@if ( $topic_mode == 'forum' || $topic_mode == 'subscription' )
	<td class="lastpost topic">
		<a href="{{ $topic->forum->url }}">{{{ $topic->forum->name }}}</a>
	</td>
	@endif
	
	@if ( $topic_mode != 'forum' )
	<td class="lastpost topic">
		{{ $topic->latest_post['date'] }}
		<br>
	
		<a href="{{ $topic->latest_post['author']->url }}" title="{{{ $topic->latest_post['author']->name }}}'s profile">{{{ $topic->latest_post['author']->name }}} </a>
		<a href="{{ $topic->latest_post['url'] }}"><img src="{{ $skin }}icons/latest_reply.png" alt="-&gt;" title="Go to last post"></a>
	</td>
	@endif

	<td class="posts">
		{{ number_format($topic->replies) }}
	</td>

	<td class="posts">
		{{ number_format($topic->views) }}
	</td>

</tr>
