<?php
class BBCode
{
	protected static $_code_regex = '%\[code\](\n?)((?:[^[]*(?:\[(?!/?code\])[^[]*)*| (?R))*)\[/code\](\n?)%isxu';
	protected static $_quote_regex = '/\[quote=([^\]]*?)\](\n?)([^[]*(?:\[(?!\/?quote(.*?)\])[^[]*)*)\[\/quote\](\n?)/isxu';
	
	public function __construct() {
	}

	/**
	 * Scales down large images
	 * This should be run before any bb code gets stored in the database or previewed
	 */
	public static function prepare( $text )
	{
		$text = preg_replace_callback('/\[img\](.*?)\[\/img\]/isu', 'BBCode::_scale_images', $text);
		return $text;
	}

	/**
	 * Undo database preparation - for editing posts which were modified by prepare_bb
	 */
	public static function undo_prepare( $text )
	{
		$text = preg_replace('/\[img:([0-9]*):([0-9]*)\](.*?)\[\/img\]/isu', '[img]$3[/img]', $text);
		
		// @todo for now, keeping this here because I assume it will go inside an input/textarea where it needs this anyway
		$text = htmlspecialchars($text);
		return $text;
	}

	/**
	 * Formats text for display. In order:
	 *   Turns \r\n into \n so that all line breaks are consistent
	 *   Encodes [code] tag content. Must come before anything else because inner content is not touched
	 *   Parses smileys. Must come before htmlspecialchars for issues with ") turning into &quot;)
	 *   htmlspecialchars. Must come before URL links (so & becomes &amp;) and before conversion to HTML tags
	 *   Encodes [img], [video], and [url] links. Must come before non-tagged link detection
	 *   Simple tag replacements converting [] to <> HTMl tags.
	 *   Non-tagged link detection and '?' character conversion. Must come before [img], [video], and [url] tags are decoded
	 *   Parses quotes.
     *   Converts line breaks to <br> tags.
	 *   Decodes [img], [video], and [url] links.
	 *   Decodes [code] content. Must come after everything else. Runs htmlspecialchars internally
	 */
	public static function parse( $text, $smileys = true, $text_only = false )
	{
		$pd = new Parsedown();
		return $pd->text($text);


		// For consistency
		$text = str_replace("\r\n", "\n", $text);

		// Encode blocks of code to protect from any changes (code, url, img tags)
		// @todo only problem seems to be if square brackets [ ] are in a URL
		$text = preg_replace_callback(BBCode::$_code_regex, 'BBCode::_protect_code', $text);

		if( $smileys ) {
			$text = BBCode::_parse_smileys($text);
		}
		$text = htmlspecialchars($text);

		if( $text_only ) {
			$text = preg_replace('/\[img(:?)([0-9]*)(:?)([0-9]*)\](.*?)\[\/img\]/isu', '$5', $text);
			$text = preg_replace('/\[(video|youtube)\](.*?)\[\/(video|youtube)\]/iu', '$2', $text);
		}
		else {
			$text = preg_replace_callback('/\[img(:?)([0-9]*)(:?)([0-9]*)\](.*?)\[\/img\]/isu', 'BBCode::_protect_images', $text);
			$text = preg_replace_callback('/\[(video|youtube)\](.*?)\[\/(video|youtube)\]/iu', 'BBCode::_protect_videos', $text);
		}
		$codes = array(
			'/\[url\](.*?)\[\/url\]/isu',
			'/\[url=(.*?)\](.*?)\[\/url\]/isu',
		);
		foreach( $codes as $code ) {
			$text = BBCode::_protect_urls( $code, $text );
		}

		// @todo overlapping size and color breaks (both closing span tags)... maybe use a different tag for one?
		// Simple regex replacements
		$codes = array(
			array( '\[b\]', '\[\/b\]', '<b>$1</b>' ),
			array( '\[i\]', '\[\/i\]', '<i>$1</i>' ),
			array( '\[u\]', '\[\/u\]', '<u>$1</u>' ),
			array( '\[strike\]', '\[\/strike\]', '<del>$1</del>' ),
			array( '\[center\](\n?)', '\[\/center\](\n?)', '<div style="text-align:center">$2</div>' ),
			array( '\[left\](\n?)', '\[\/left\](\n?)', '<div style="text-align:left">$2</div>' ),
			array( '\[right\](\n?)', '\[\/right\](\n?)', '<div style="text-align:right">$2</div>' ),
			array( '\[size=([1-3]?[0-9])\]', '\[\/size\]', '<span style="font-size:$1pt">$2</span>' ),
			array( '\[colo(u?)r=(\#[0-9A-F]{6}|#[0-9A-F]{3}|[a-z]+)\]', '\[\/colo(u?)r\]', '<span style="color:$2">$3</span>' ),
			array( '\[list\](\n?)', '\[\/list\](\n?)', '<ul>$2</ul>' ),
			array( '\[list=([a1])\](\n?)', '\[\/list\](\n?)', '<ol type="$1">$3</ol>' ),
			array( '\[spoiler\](\n?)', '\[\/spoiler\]', '<a href="" class="spoiler-alert">SPOILER ALERT. Click here to see text.</a><div class="spoiler">$2</div>' ),
			array( '\[smiley\]', '\[\/smiley\]', '<img src="$1" class="smiley">' ),
			array( '\[quote\](\n?)', '\[\/quote\](\n?)', '<div class="quote"><div class="author">Quote</div><div class="content">$2</div></div>' ),
		);
		foreach( $codes as $code ) {
			$text = BBCode::_simple_replace( $code, $text );
		}
		$text = str_replace('[*]', '<li>', $text);
		
		// Detect URLs that are not within [url] tags
		$text = str_replace('?', '&#63;', $text);
		$text = preg_replace_callback('#([\w]+?://[\w\#$%&~/.\-;:=,+]*)#is', 'BBCode::_detect_urls', $text);
		$text = preg_replace_callback('#((www|ftp)\.[\w\#$%&~/.\-;:=,+]*)#is', 'BBCode::_detect_urls', $text);

		// More complex stuff including decoding tags that were protected		
		$count = 0;
		do {
			$text = preg_replace_callback(BBCode::$_quote_regex, 'BBCode::_embed_quotes', $text, -1, $count);
		} while ( $count > 0 );

		$text = nl2br($text);
		$text = preg_replace_callback('/\[img(:?)([0-9]*)(:?)([0-9]*)\](.*?)\[\/img\]/su', 'BBCode::_embed_images', $text);
		$text = preg_replace_callback('/\[video\](.*?)\[\/video\]/iu', 'BBCode::_embed_videos', $text);
		$text = preg_replace_callback('/\[#url=(.*?)#\](.*?)\[#\/url#\]/isu', 'BBCode::_embed_urls', $text);
		$text = preg_replace_callback(BBCode::$_code_regex, 'BBCode::_embed_code', $text);

		return $text;
	}

	/**
	 * Removes all bbcode tags
	 */
	public static function simplify( $text ) {
		$text = BBCode::strip_quotes($text);
		$text = str_replace(array('[', ']'), array('<', '>'), $text);
		$text = htmlspecialchars(trim(strip_tags($text)));
		return $text;
	}

	/**
	 * Removes [quote] blocks (and the content inside)
	 */
	public static function strip_quotes( $text ) {
		$text = preg_replace('/\[quote\](.*?)\[\/quote\]/isu', '', $text);
		$count = 0;
		do {
			$text = preg_replace(BBCode::$_quote_regex, '', $text, -1, $count);
		} while ( $count > 0 );
		return trim($text);
	}

	/**
	 * Replaces any [x][/x] tags with <y></y>
	 */
	protected static function _simple_replace( $code, $text ) {
		$count = 0;
		do {
			$text = preg_replace("/{$code[0]}(.*?){$code[1]}/isu", $code[2], $text, -1, $count);
		} while ( $count > 0 );
		
		return $text;
	}

	/**
	 * Loads and sorts smileys
	 */
	public static function load_smileys()
	{
		$smileys = Smiley::all();

		usort($smileys, 'BBCode::_sort_smileys');
		
		return $smileys;
	}
	
	/**
	 * Sorts smileys so that those starting with the same characters don't conflict
	 */
	protected static function _sort_smileys($a, $b) {
		if( strlen($a->code) == strlen($b->code) ) {
			return 0;
		}
		return ( strlen($a->code) > strlen($b->code) ) ? -1 : 1;
	}

	/**
	 * Replace all smiley codes with images
	 */
	protected static function _parse_smileys( $text ) {
		global $me;
		
		// Smileys disabled
		if( $me->loggedin && !$me->enable_smileys ) {
			return $text;
		}
		$orig = $repl = array();
		
		// @todo cache this, you're calling it every time!
		$smileys = BBCode::load_smileys();

		foreach( $smileys as $smiley ) {
			$orig[] = $smiley->code;
			$repl[] = '[smiley]/images/smileys/' . $smiley->file . '[/smiley]';
		}
		if( count($orig) ) {
			$text = str_replace($orig, $repl, $text);
		}
		return $text;
	}
	
	/**
	 * Encodes any text within [video] tags so that URLs aren't auto-detected
	 */
	protected static function _protect_videos( $matches ) {
		$url = $matches[2];
		return '[video]' . base64_encode($url) . '[/video]';
	}

	/**
	 * Turns Youtube and Vimeo links into embedded iframes
	 */
	protected static function _embed_videos( $matches ) {
		$url = base64_decode($matches[1]);
		if( !preg_match('#(f|ht)tp(s?)://#i', $url) ) {
			$url = 'http://' . $url;
		}
		$parts = parse_url(str_replace('&amp;', '&', $url));

		switch( $parts['host'] ) {
			case 'vimeo.com':
				$video_id = ltrim($parts['path'], '/'); $source = 'vimeo'; break;

			case 'youtu.be':
				$video_id = ltrim($parts['path'], '/'); $source = 'youtube'; break;

			case 'youtube.com':
			case 'www.youtube.com':
				parse_str($parts['query'], $params); $video_id = $params['v']; $source = 'youtube'; break;

			default:
				$video_id = str_replace('http://', '', $url); $source = 'youtube'; break;
		}

		switch( $source ) {
			case 'vimeo':
				return '<iframe width="640" height="390" src="http://player.vimeo.com/video/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
				break;
			
			case 'youtube':
			default:
				return '<iframe width="640" height="390" src="http://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
				break;
		}
	}

	/**
	 * Formats quote blocks
	 */
	protected static function _embed_quotes( $matches ) {
		global $_CONFIG;
		$skin = '/images/skins/' . Config::get('app.skin') . '/';

		$parts = explode(';', $matches[1]);
		if( count($parts) > 1 ) {
			$post = array_pop($parts);
			if( $post == '' ) { $parts[] = ''; $post = null; }
		}
		$author = implode(';', $parts);
		if( substr($author, 0, 6) == '&quot;' ) { $author = substr($author, 6); }
		if( substr($author, -6) == '&quot;' ) { $author = substr($author, 0, -6); }

		$text = '<div class="quote"><div class="author">' . $author . ' wrote';
		if( $post ) {
			$text .= '<a href="/posts/' . $post . '#' . $post . '"><img src="' . $skin . 'icons/jump.png"></a>';
		}
		$text .= '</div><div class="content">' . $matches[3] . '</div></div>';

		return $text;
	}

	/**
	 * Encodes any text within [code] tags so that other replacements don't apply
	 */
	protected static function _protect_code( $matches ) {
		$text = $matches[2];
		return '[code]' . base64_encode($text) . '[/code]';
	}

	/**
	 * Undo the encoding from protect_code
	 */
	protected static function _embed_code( $matches ) {
		$text = $matches[2];
		return '<pre class="code">' . htmlspecialchars(base64_decode($text)) . '</pre>';
	}

	/**
	 * Encodes any text within [img] tags so that URLs aren't auto-detected
	 */
	protected static function _protect_images( $matches ) {
		$text = str_replace(' ', '%20', $matches[5]);
		$dimensions = implode('', array_splice($matches, 1, 4));
		return '[img' . $dimensions . ']' . base64_encode($text) . '[/img]';
	}

	/**
	 * Undo the encoding from protect_images
	 */
	protected static function _embed_images( $matches ) {
		$url = $matches[5];
		$width = $matches[2]; $height = $matches[4];
		$url = base64_decode($url);

		if( $width && $height ) {
			return '<div class="scaled" style="width:' . $width . 'px;"><a class="scaled" href="' . $url . '" target="_blank">Image scaled. Click here to view full size</a></div>
				<img class="post-image" src="' . $url . '" width="' . $width . '" height="' . $height . '">';
		} else {
			return '<img class="post-image" src="' . $url . '">';
		}
	}

	/**
	 * Scales down large images
	 */
	protected static function _scale_images( $matches ) {
		global $_CONFIG;
		$url = $matches[1];
		list($width, $height, $type, $attr) = getimagesize($url);

		if( $width > 800 ) {
			$prop = $width / 800;
			$h = (int)($height / $prop);
			$w = 800;
		}
		
		if( $w != $width || $h != $height ) {
			return "[img:{$w}:{$h}]{$url}[/img]";
		} else {
			return "[img]{$url}[/img]";
		}
	}

	/**
	 * Encodes any text within [url] tags so that URLs aren't auto-detected
	 */
	protected static function _protect_urls( $code, $text ) {
		$count = 0;
		do {
			$text = preg_replace_callback($code, 'BBCode::_encode_urls', $text, -1, $count);
		} while ( $count > 0 );
		
		return $text;
	}

	protected static function _encode_urls( $matches ) {
		// can be [url=link] or [url="link"]
		$url = $matches[1];
		if( substr($url, 0, 6) == '&quot;' ) { $url = substr($url, 6); }
		if( substr($url, -6) == '&quot;' ) { $url = substr($url, 0, -6); }

		$url = str_replace(' ', '%20', $url);
		$text = isset($matches[2]) ? $matches[2] : base64_encode($url);
		return '[#url=' . base64_encode($url) . '#]' . $text . '[#/url#]';
	}

	protected static function _detect_urls( $matches ) {
		$url = $matches[1];
		$url = base64_encode($url);
		return '[#url=' . $url . '#]' . $url . '[#/url#]';
	}

	/**
	 * Undo the encoding from protect_urls, protect from malicious links, and shorten long urls
	 */
	protected static function _embed_urls( $matches ) {
		global $_CONFIG;

		$url = $matches[1];
		
		// Shorten the text if it's just a copy of the URL and the URL is long
		if( $matches[2] == $url ) {
			$text = base64_decode($url);
			if( strlen($text) > 60 ) {
				$text = substr($text, 0, 60) . '...';
			}
		} else {
			$text = $matches[2];
		}
		$url = base64_decode($url);

		// Make sure it starts with a legal scheme
		if( !preg_match('#(f|ht)tp(s?)://#i', $url) ) {
			$url = 'http://' . $url;
			$url = preg_replace('/(javascript|script|about|applet|activex|chrome):/isu', '', $url);
		}

		// Open in new tab if it's not on my domain
		if( substr($url, 0, strlen('http://' . $_CONFIG['domain'])) != ( 'http://' . $_CONFIG['domain'] )) {
			return '<a href="' . $url . '" target="_blank" rel="nofollow">' . $text . '</a>';
		} else {
			return '<a href="' . $url . '">' . $text . '</a>';
		}
	}

	/**
	 * Display the BBCode buttons
	 */
	public static function show_bbcode_controls()
	{
		$colors = array(
			'000000', 'a0522d', '556b2f', '006400', '2f4f4f', '000080', '4b0082', '696969', 
			'8b0000', 'ff8c00', '808000', '008000', '008080', '0000ff', '483d8b', '808080', 
			'ff0000', 'f4a460', '9acd32', '2e8b57', '48d1cc', '4169e1', '800080', '708090', 
			'ff00ff', 'ffa500', 'ffff00', '00ff00', '00ffff', '00bfff', '9932cc', 'c0c0c0', 
			'ffc0cb', 'f5deb3', 'fffacd', '98fb98', 'afeeee', 'add8e6', 'dda0dd', 'ffffff'
		);
		$sizes = array(8, 10, 12, 16, 20, 24, 30);

		echo View::make('blocks.bbcode')
			->with('colors', $colors)
			->with('sizes', $sizes);
	}

	/**
	 * Display the smiley buttons next to textarea with a link to popup more
	 */
	public static function show_smiley_controls()
	{
		$smileys = Smiley::where('show', '=', 2)
			->orderBy('order', 'asc')
			->take(25)
			->get();

		echo View::make('blocks.smileys')
			->with('smileys', $smileys);
	}
}
