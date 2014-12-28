@if ( count($attachments) > 0 )
<div class="panel panel-default">

    <div class="panel-heading">Attachments</div>

    <div class="panel-body">
        <?php $prev_type = 0; ?>
        @foreach ( $attachments as $attachment )
            @if ( $attachment->filetype == 1 )
                @if ( $prev_type == 0 )<div style="padding:0 5px">@endif
                <div><a href="{{ $attachment->url }}">{{{ $attachment->origfilename }}}</a> ({{ $attachment->size }}) -
                <a href="" class="delete-attachment" data-id="{{ $attachment->id }}">Remove</a></div>
                <div class="clearfix"></div>
            @else
                @if ( $prev_type == 1 )</div><br>@endif
                <div class="photo" style="height:192px">
                <a class="thumb" href="{{ $attachment->url }}">
                <img src="{{ $cdn }}{{ $attachment->thumbnail }}"></a>
                <a href="" class="delete-attachment" data-id="{{ $attachment->id }}">Remove</a></div>
            @endif
            <?php $prev_type = $attachment->filetype; ?>
        @endforeach

        <div class="clearfix"></div>
    </div>
</div>
@endif
