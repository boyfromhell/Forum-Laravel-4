<div class="moderate">
<a href="/admin/topics.php?mode=move&t={{ $topic->id }}"><img src="/images/buttons/move.png" alt="Move Topic" title="Move topic to a different forum" width="35" height="35"></a>
<a href="/admin/topics.php?mode=delete&t={{ $topic->id }}"><img src="/images/buttons/delete.png" alt="Delete Topic" title="Delete topic" width="35" height="35"></a>
@if ( $topic->status == 1 )
<a href="/admin/topics.php?mode=unlock&t={{ $topic->id }}"><img src="/images/buttons/unlock.png" alt="Unlock Topic" title="Unlock topic" width="35" height="35"></a>
@else
<a href="/admin/topics.php?mode=lock&t={{ $topic->id }}"><img src="/images/buttons/lock.png" alt="Lock Topic" title="Lock topic from replies" width="35" height="35"></a>
@endif
{{-- <a href="/admin/topics.php?mode=split&t={$topic->id}"><img src="/images/buttons/split.png" alt="Split Topic"></a> --}}

<div class="clearfix"></div>
</div>

