<tr class="thread-row{{ $topic->unread_post->id ? ' unread' : '' }}" data-id="{{ $topic->id }}" data-all="1" data-read="{{ $topic->unread_post->id ? '1' : '0' }}" data-title="{{{ $topic->title }}}">

	@if ($show_checkbox)
	<td class="icon">
		{{ Form::checkbox('topics[]', $topic->id) }}
	</td>
	@endif

	<td class="icon">
		<div class="icon {{{ $topic->image }}}" title="{{{ $topic->alt_text }}}"></div>
	</td>

	<td class="icon hidden-xs">
		@if ( $topic->smiley )
			<img src="{{{ $topic->smiley_img }}}" alt="{{{ $topic->smiley_alt }}}">
		@else
			&nbsp;
		@endif
	</td>

	<td style="min-width:{{ $show_forum && $show_last_post ? '30%' : '50%' }}" class="topic">
		@if ( $topic->has_attachments )
		<span class="glyphicon glyphicon-paperclip"></span>
		@endif
		
		@if ( $topic->unread_post->id )<a href="{{ $topic->unread_post->url }}" title="Go to first unread post"><span class="glyphicon glyphicon-arrow-right"></span><span class="glyphicon glyphicon-file"></span></a> @endif

		@if ( $topic->type == 2 )
		<span class="label label-danger">Announcement</span>
		@elseif ( $topic->type == 1 )
		<span class="label label-default">Sticky</span>
		@elseif ( $topic->has_poll )
		<span class="label label-primary">Poll</span>
		@endif

		{{{ $topic->prefix }}}
		<a href="{{ $topic->url }}" title="Go to topic">{{{ $topic->short_title }}}</a>
	
		@if ( $topic->pages > 1 )
			<small> ( <span class="glyphicon glyphicon-file"></span> 
	
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

	@if ($show_forum)
	<td>
		<a href="{{ $topic->forum->url }}">{{{ $topic->forum->name }}}</a>
	</td>
	@endif
	
	@if ($show_last_post)
	<td class="lastpost topic hidden-xs">
		{{ $topic->latest_post->date }}
		<br>
	
		<a href="{{ $topic->latest_post->user->url }}" title="{{{ $topic->latest_post->user->name }}}'s profile">{{{ $topic->latest_post->user->name }}} </a>
		<a href="{{ $topic->latest_post->url }}"><img src="{{ $skin }}icons/latest_reply.png" alt="-&gt;" title="Go to last post"></a>
	</td>
	@endif

	<td class="posts hidden-xs">
		{{ number_format($topic->replies) }}
	</td>

	<td class="posts hidden-xs">
		{{ number_format($topic->views) }}
	</td>

</tr>
