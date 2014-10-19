<div id="colorBox" class="colorbox">
@foreach ( $colors as $count => $color )
	<div class="bbcolor" onClick="addtext('[color=#{{ $color }}]', '[/color]', 0); hideboxes();"><div style="border:1px solid #aca899; height:12px; width:12px; background:#{{ $color }}"></div></div>
	@if ( $count%8 == 7 )
		<div style="clear:both"></div>
	@endif
@endforeach
</div>

<div id="sizeBox" class="colorbox">
@foreach ( $sizes as $size )
	<div class="bbcolor" style="cursor:default; clear:both; width:48px; height:{{ $size }}pt" onClick="addtext('[size={{ $size }}]', '[/size]', 0); hideboxes();"><span style="font-size:{{ $size }}pt; line-height:{{ $size }}pt; vertical-align:top">{{ $size }}</span></div>
@endforeach
</div>

{{-- todo display smiley button in certain places --}}
@if ( false )
<div class="bbcode" onClick="window.open('/forum/smileys', 'smileys', 'width=300, height=300, resizable=yes, scrollbars=yes, toolbar=no, location=no, directories=no, status=yes, menubar=no' ); return false;"><img src="/images/bbcode/smile.gif" title="Smileys"></div>
@endif

<div class="bbcode" onClick="stripbbtags();"><img src="/images/bbcode/clear.gif" title="Clear Formatting"></div>
<div class="bbsep"></div>
	
<div class="btn-group btn-group-sm">
	<button type="button" onClick="addtext('[b]','[/b]',0);" class="btn btn-default" title="Bold">
		<span class="glyphicon glyphicon-bold"></span>
	</button>
	<button type="button" onClick="addtext('[i]','[/i]',0);" class="btn btn-default" title="Italic">
		<span class="glyphicon glyphicon-italic"></span>
	</button>
	<button type="button" onClick="addtext('[u]','[/u]',0);" class="btn btn-default" title="Underline">
		<img src="/images/bbcode/underline.gif">
	</button>
	<button type="button" onClick="addtext('[strike]','[/strike]',0);" class="btn btn-default" title="Strike">
		<img src="/images/bbcode/strike.gif">
	</button>
</div>

<div class="btn-group btn-group-sm">
	<button type="button" id="sizebb" onMouseOver="sizeover();" onMouseOut="sizeout();" onClick="showsizes();" class="btn btn-default" title="Font Size">
		<span class="glyphicon glyphicon-text-height"></span>
	</button>
	<button type="button" id="colorbb" onMouseOver="colorover();" onMouseOut="colorout();" onClick="showcolors();" class="btn btn-default" title="Font Color">
		<span class="glyphicon glyphicon-tint"></span>
	</button>
</div>

<div class="btn-group btn-group-sm">
	<button type="button" onClick="addtext('[left]','[/left]',0);" class="btn btn-default" title="Left Align">
		<span class="glyphicon glyphicon-align-left"></span>
	</button>
	<button type="button" onClick="addtext('[center]','[/center]',0);" class="btn btn-default" title="Center Align">
		<span class="glyphicon glyphicon-align-center"></span>
	</button>
	<button type="button" onClick="addtext('[right]','[/right]',0);" class="btn btn-default" title="Right Aligh">
		<span class="glyphicon glyphicon-align-right"></span>
	</button>
</div>

<div class="bbcode" onClick="addtext('[quote]','[/quote]',0);"><img src="/images/bbcode/bbquote.gif" title="Quote"></div>
<div class="bbcode" onClick="addtext('[code]','[/code]',0);"><img src="/images/bbcode/code.gif" title="Code"></div>
<div class="bbcode" onClick="addtext('[img]','[/img]',0);"><img src="/images/bbcode/img.gif" title="Image"></div>
<div class="bbcode" onClick="addtext('[url=http://]','[/url]',0);"><img src="/images/bbcode/url.gif" title="URL"></div>
<div class="bbcode" onClick="addtext('[list=1]\n[*]','\n[/list]',0);"><img src="/images/bbcode/list.gif" title="List"></div>
<div class="bbcode" onClick="addtext('[list]\n[*]','\n[/list]',0);"><img src="/images/bbcode/bullet.gif" title="Bullets"></div>
	
@if ( $_PAGE['category'] != 'usercp' )
	<div class="bbsep"></div>
	<div class="bbcode" onClick="addtext('[video]','[/video]',0);"><img src="/images/bbcode/ytube.gif" title="Video"></div>
	<div class="bbcode" onClick="addtext('[spoiler]','[/spoiler]',0);"><img src="/images/bbcode/spoiler.gif" title="Spoiler"></div>
@endif
