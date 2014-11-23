<div class="btn-toolbar" role="toolbar">

<div class="btn-group btn-group-sm">
	<button type="button" onClick="stripbbtags();" class="btn btn-default" title="Clear Formatting">
		<span class="glyphicon glyphicon-ban-circle"></span>
	</button>
</div>

<div class="btn-group btn-group-sm">
	<button type="button" onClick="addtext('**','**',0);" class="btn btn-default" title="Bold">
		<span class="glyphicon glyphicon-bold"></span>
	</button>
	<button type="button" onClick="addtext('*','*',0);" class="btn btn-default" title="Italic">
		<span class="glyphicon glyphicon-italic"></span>
	</button>
	<button type="button" onClick="addtext('[u]','[/u]',0);" class="btn btn-default btn-bbcode" title="Underline">
		<span class="bbcode-underline">U</span>
	</button>
	<button type="button" onClick="addtext('~~','~~',0);" class="btn btn-default btn-bbcode" title="Strikethrough">
		<span class="bbcode-strikethrough">S</span>
	</button>
</div>

<div class="btn-group btn-group-sm">
	<button type="button" class="btn btn-default dropdown-toggle" title="Font Size" data-toggle="dropdown">
		<span class="glyphicon glyphicon-text-height"></span>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu bbcode-menu bbcode-size">
@foreach ( $sizes as $size )
	    <li onClick="addtext('[size={{ $size }}]', '[/size]', 0);"><span style="font-size:{{ $size }}pt; line-height:1">{{ $size }}</li>
@endforeach
	</ul>
</div>

<div class="btn-group btn-group-sm">
	<button type="button" class="btn btn-default dropdown-toggle" title="Font Color" data-toggle="dropdown">
		<span class="glyphicon glyphicon-tint"></span>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu bbcode-menu bbcode-color">
@foreach ( $colors as $count => $color )
		<li onClick="addtext('[color=#{{ $color }}]', '[/color]', 0);"><span style="background-color:#{{ $color }}"></span></li>
	@if ( $count%8 == 7 )
	@endif
@endforeach
	</ul>
</div>

<div class="btn-group btn-group-sm">
	<button type="button" onClick="addtext('[left]','[/left]',0);" class="btn btn-default" title="Left Align">
		<span class="glyphicon glyphicon-align-left"></span>
	</button>
	<button type="button" onClick="addtext('[center]','[/center]',0);" class="btn btn-default" title="Center Align">
		<span class="glyphicon glyphicon-align-center"></span>
	</button>
	<button type="button" onClick="addtext('[right]','[/right]',0);" class="btn btn-default" title="Right Align">
		<span class="glyphicon glyphicon-align-right"></span>
	</button>
</div>

<div class="btn-group btn-group-sm">
	<button type="button" onClick="addtext('> ','',0);" class="btn btn-default btn-bbcode" title="Quote">
		<span class="bbcode-quote">&#8220;</span>
	</button>
	<button type="button" onClick="addtext('[code]','[/code]',0);" class="btn btn-default" title="Code">
		<span class="glyphicon glyphicon-tower"></span>
	</button>
	<button type="button" onClick="addtext('![](',')',0);" class="btn btn-default" title="Image">
		<span class="glyphicon glyphicon-picture"></span>
	</button>
	<button type="button" onClick="addtext('[text](',')',0);" class="btn btn-default" title="URL">
		<span class="glyphicon glyphicon-link"></span>
	</button>
	<button type="button" onClick="addtext('1. ','',0);" class="btn btn-default btn-bbcode" title="List">
		<span class="bbcode-ol">1<br>2</span>
	</button>
	<button type="button" onClick="addtext('* ','',0);" class="btn btn-default" title="Bullets">
		<span class="glyphicon glyphicon-list"></span>
	</button>
</div>
	
@if ( $_PAGE['category'] != 'usercp' )
<div class="btn-group btn-group-sm">
	<button type="button" onClick="addtext('[video]','[/video]',0);" class="btn btn-default" title="Video">
		<span class="glyphicon glyphicon-facetime-video"></span>
	</button>
	<button type="button" onClick="addtext('[spoiler]','[/spoiler]',0);" class="btn btn-default" title="Spoiler">
		Spoiler
	</button>
</div>
@endif

</div>
