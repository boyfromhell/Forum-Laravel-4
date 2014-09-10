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
	

<div class="bbcode" onClick="addtext('[b]','[/b]',0);"><img src="/images/bbcode/bold.gif" title="Bold"></div>
<div class="bbcode" onClick="addtext('[i]','[/i]',0);"><img src="/images/bbcode/italic.gif" title="Italic"></div>
<div class="bbcode" onClick="addtext('[u]','[/u]',0);"><img src="/images/bbcode/underline.gif" title="Underline"></div>
<div class="bbcode" onClick="addtext('[strike]','[/strike]',0);"><img src="/images/bbcode/strike.gif" title="Strikethrough"></div>
<div class="bbsep"></div>

<div id="sizebb" class="bbcode" onMouseOver="sizeover();" onMouseOut="sizeout();" onClick="showsizes();"><img id="sizeimg" src="/images/bbcode/size.gif" title="Font Size" onClick="showsizes();"></div>
<div id="colorbb" class="bbcode" onMouseOver="colorover();" onMouseOut="colorout();" onClick="showcolors();"><img id="colorimg" src="/images/bbcode/color.gif" title="Font Color" onClick="showcolors();"></div>
<div class="bbsep"></div>
	
<div class="bbcode" onClick="addtext('[left]','[/left]',0);"><img src="/images/bbcode/left.gif" title="Left Align"></div>
<div class="bbcode" onClick="addtext('[center]','[/center]',0);"><img src="/images/bbcode/center.gif" title="Center Align"></div>
<div class="bbcode" onClick="addtext('[right]','[/right]',0);"><img src="/images/bbcode/right.gif" title="Right Align"></div>
<div class="bbsep"></div>
	
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
