<div class="form-group">
	<label class="col-sm-3 control-label">Attachments</label>
	<div class="col-sm-9">
		<p class="form-control-static">
	Select up to <b>{{ $max_file_uploads }}</b> files, <b>{{ $upload_max_filesize }} MB</b> each. Total limit is <b>{{ $post_max_size }} MB</b><br>

		<input type="file" id="files" name="files[]" multiple />
		</p>
		<output id="list"></output>
	{{--	<input name="attach" id="attach" type="submit" value="Attach Files" style="display:none">--}}
	</div>
</div>

<script type="text/javascript">
function handleFileSelect(evt) {
	var total = 0, count = 0;
	var files = evt.target.files; // FileList object

	// files is a FileList of File objects. List some properties.
	var output = [];
	for (var i = 0, f; f = files[i]; i++) {
		var content = '<li><strong>' + escape(f.name) + '</strong> (' + (f.type || 'n/a') + ') - ' + format_size(f.size);
		if( f.size > {{ $max_bytes }} ) {
			content += ' <span style="color:#a00">(TOO LARGE)</span>';
		}
		content += '</li>';
		output.push(content);
		total += f.size;
		count++;
	}
	if( count > 0 ) {
		var html = '<ul>' + output.join('') + '</ul>' + count + ' files, ' + format_size(total);
		if( count > {{ $max_file_uploads }} ) {
			html = '<br><span style="color:#a00"><b>You have selected too many files</b></span>';
		}
		if( total > {{ $max_total }} ) {
			html = '<br><span style="color:#a00"><b>The total size for these files is too large</b></span>';
		}
		$('#list').html(html);
		$('#attach').show();
	} else {
		$('#list').html('');
		$('#attach').hide();
	}
}

document.getElementById('files').addEventListener('change', handleFileSelect, false);
</script>
