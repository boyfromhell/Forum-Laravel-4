{if !$is_mobile}
{include file='blocks/user_menu.tpl' content_id=$message->id user=$message->from}
{/if}

<div class="panel panel-primary">

	<div class="panel-heading">

		<div style="float:left">{datestring($message->date_sent, 1)}<a name="{$message->id}"></a></div>
		
		<div style="float:right">#{$message->count}</div>

	</div>

	<div class="recipients">
		To: 
		{foreach $message->users as $count => $user}
			<a href="{$user->url}">{htmlspecialchars($user->name)}</a>{if $count < count($message->users)-1}, {/if}
		{/foreach}
	</div>

	@include ('users.row', ['content_id' => $message->id, 'user' => $message->from]) }}

	<div class="panel-body" id="pt{$message->id}">

		<div id="post{$message->id}">
	
			{{ BBCode::parse($message->content) }}

		</div>
	
		<div class="pull-right">
			<a href="/messages/compose?p={{ $message->id }}" class="btn btn-default btn-xs">Quote</a>
			<a href="/delete.php?pm={{ $message->id }}" class="btn btn-danger btn-xs">x</a>
		</div>

	</div>

	@if ( $message->from->sig && $message->from->attach_sig )
	<div class="panel-footer sig">
		{{ BBCode::parse($message->from->sig) }}
	</div>
	@endif

	@include ('posts.attachments', ['post' => $message])
	</div>
	
</div>

