@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">IRC Chat</div>

	<div class="panel-body">

	Join your fellow IVANers in IRC chat:<br><br>

	<b>Server:</b> irc.freenode.net<br>
	<b>Room:</b> #attnam<br><br>

	Chat Log: <a href="http://log.makhleb.net/" rel="nofollow" target="_blank">http://log.makhleb.net/</a><br>
	Chat Statistics: <a href="http://stats.makhleb.net/" rel="nofollow" target="_blank">http://stats.makhleb.net/</a><br><br>

	@if ( !$is_mobile )
	You may also use the Java chat applet embedded in this website:<br><br>
	
	<a href="/community/chat" class="btn btn-primary" onClick="window.open('/chat-popup', 'chat', 'width=800, height=575, resizable=yes, scrollbars=yes, toolbar=no, location=no, directories=no, status=yes, menubar=no'); return false;">Java Chatroom</a><br><br>
	@endif

	</div>

</div>

@stop
