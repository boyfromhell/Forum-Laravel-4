<!DOCTYPE html>
<html>
<head>
<title>{{{ $_PAGE['title'] }}}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 

	<link rel="stylesheet" href="/css/global.css?v={{ $versions['css'] }}">
	
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script type="text/javascript" src="/js/global.js?v={{ $versions['js'] }}"></script>
	
	@include ('custom.analytics')
	
	<script type="text/javascript">
	$(document).ready(function() {
		parangi.init();
	});
	</script>

	<base target="_top">
</head>

@yield ('content')

</html>
