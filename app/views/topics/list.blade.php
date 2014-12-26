    <table class="table">
    <thead>
    <tr>
        <th class="icon">&nbsp;</th>
        <th class="icon hidden-xs">&nbsp;</th>
        <th style="min-width:50%">{{ $show_forum ? 'Topic' : 'Topics' }}</th>
		@if ($show_forum)
		<th class="hidden-xs">Forum</th>
		@endif
		@if ($show_last_post)
        <th class="hidden-xs">Last Post</th>
		@endif
        <th class="posts hidden-xs">Replies</th>
        <th class="posts hidden-xs">Views</th>
    </tr>
    </thead>
    <tbody>
    @foreach ( $topics as $topic )
        @include ('topics.row')
    @endforeach
    </tbody>
    </table>
