@extends('layout')

@section('content')

<div class="welcome ">

	<div class="header">IRC Chat</div>

	<div class="body">

	Join your fellow IVANers in IRC chat:<br><br>

	<b>Server:</b> irc.freenode.net<br>
	<b>Room:</b> #attnam<br><br>

	Chat Log: <a href="http://log.makhleb.net/">http://log.makhleb.net/</a><br>
	Chat Statistics: <a href="http://stats.makhleb.net/">http://stats.makhleb.net/</a><br><br>

	@if ( !$is_mobile )
	You may also use the Java chat applet embedded in this website:<br><br>
	
	<a href="/community/chat" class="button" onClick="window.open('/chat-popup', 'chat', 'width=800, height=575, resizable=yes, scrollbars=yes, toolbar=no, location=no, directories=no, status=yes, menubar=no'); return false;">Java Chatroom</a><br><br>
	@endif

	<div class="break"></div>
	
	</div>

</div>

@stop
