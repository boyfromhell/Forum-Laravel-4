@extends('minimal')

@section('content')

<body>

<applet code="IRCApplet.class" archive="irc.jar,pixx.jar" codebase="/pjirc/" width="100%" height="550">
<param name="CABINETS" value="irc.cab, securedirc.cab, pixx.cab">
<param name="language" value="english">
<param name="nick" value="{{{ $nick }}}">
<param name="alternatenick" value="{{{ $nick }}}???">
<param name="host" value="irc.freenode.net">
<param name="port" value="6667">
<param name="name" value="{{{ $nick }}}">
<param name="userid" value="{{{ $nick }}}">
<param name="command1" value="/msg nickserv identify ">
<param name="command2" value="/join #attnam">
<param name="soundbeep" value="snd/bell2.au">
<param name="soundquery" value="snd/ding.au">
<param name="soundword1" value="snd/ding.au">
<param name="quitmessage" value="http://{{{ Config::get('app.domain') }}}">
<param name="highlight" value="true">
<param name="gui" value="pixx">
<param name="style:bitmapsmileys" value="false">

@foreach ( $smileys as $count => $smiley )
<param name="style:smiley{{ $count+1 }}" value="{{{ $smiley->code }}} http://{{ Config::get('app.domain') }}/images/smileys/{{ $smiley->file }}">
@endforeach

<param name="style:sourcefontrule1" value="all all SansSerif 14">
<param name="pixx:language" value="pixx-english">
<param name="pixx:highlightnick" value="true">
<param name="pixx:nickfield" value="true">
<param name="pixx:styleselector" value="true">
<param name="pixx:setfontonstyle" value="true">
<param name="pixx:timestamp" value="true">
<param name="pixx:mouseurlopen" value="1 2">
<param name="pixx:mousechanneljoin" value="1 2">
<param name="pixx:configurepopup" value="true">
<param name="pixx:popupmenustring1" value="Whois">
<param name="pixx:popupmenustring2" value="Query">
<param name="pixx:popupmenustring3" value="Ban">
<param name="pixx:popupmenustring4" value="Kick + Ban">
<param name="pixx:popupmenustring5" value="--">
<param name="pixx:popupmenustring6" value="Op">
<param name="pixx:popupmenustring7" value="DeOp">
<param name="pixx:popupmenustring8" value="HalfOp">
<param name="pixx:popupmenustring9" value="DeHalfOp">
<param name="pixx:popupmenustring10" value="Voice">
<param name="pixx:popupmenustring11" value="DeVoice">
<param name="pixx:popupmenustring12" value="--">
<param name="pixx:popupmenustring13" value="Ping">
<param name="pixx:popupmenustring14" value="Version">
<param name="pixx:popupmenustring15" value="Time">
<param name="pixx:popupmenustring16" value="Finger">
<param name="pixx:popupmenustring17" value="--">
<param name="pixx:popupmenucommand1_1" value="/Whois %1">
<param name="pixx:popupmenucommand2_1" value="/Query %1">
<param name="pixx:popupmenucommand3_1" value="/mode %2 -o %1">
<param name="pixx:popupmenucommand3_2" value="/mode %2 +b %1">
<param name="pixx:popupmenucommand4_1" value="/mode %2 -o %1">
<param name="pixx:popupmenucommand4_2" value="/mode %2 +b %1">
<param name="pixx:popupmenucommand4_3" value="/kick %2 %1">
<param name="pixx:popupmenucommand6_1" value="/mode %2 +o %1">
<param name="pixx:popupmenucommand7_1" value="/mode %2 -o %1">
<param name="pixx:popupmenucommand8_1" value="/mode %2 +h %1">
<param name="pixx:popupmenucommand9_1" value="/mode %2 -h %1">
<param name="pixx:popupmenucommand10_1" value="/mode %2 +v %1">
<param name="pixx:popupmenucommand11_1" value="/mode %2 -v %1">
<param name="pixx:popupmenucommand13_1" value="/CTCP PING %1">
<param name="pixx:popupmenucommand14_1" value="/CTCP VERSION %1">
<param name="pixx:popupmenucommand15_1" value="/CTCP TIME %1">
<param name="pixx:popupmenucommand16_1" value="/CTCP FINGER %1">
<param name="pixx:color0" value="DEE3E7">
<param name="pixx:color1" value="000000">
<param name="pixx:color2" value="DEE3E7">
<param name="pixx:color3" value="DEE3E7">
<param name="pixx:color4" value="D1D7DC">
<param name="pixx:color5" value="DEE3E7">
<param name="pixx:color6" value="E5E5E5">
<param name="pixx:color7" value="D1D7DC">
<param name="pixx:color8" value="FFA34F">
<param name="pixx:color9" value="000000">
<param name="pixx:color10" value="EFEFEF">
<param name="pixx:color11" value="FFA34F">
<param name="pixx:color12" value="599FCB">
<param name="pixx:color13" value="DEE3E7">
<param name="pixx:color14" value="DEE3E7">
<param name="pixx:color15" value="DEE3E7">
</applet>

</body>

@stop
