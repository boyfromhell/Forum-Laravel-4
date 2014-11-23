@foreach ( $forums as $jf )
	@if ( $show_external || ! $jf->external )
	<option value="{{ $jf->id }}"{{ $selected == $jf->id ? ' selected' : '' }}>
	@for( $i=0; $i<$level; $i++ )
		&nbsp;&nbsp;&nbsp;&nbsp;
	@endfor
		{{{ $jf->name }}}
	</option>
	@endif
	@include ('blocks.forum_options', ['forums' => $jf->children, 'level' => $level+1])
@endforeach
