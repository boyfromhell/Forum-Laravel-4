<?php

class Helpers
{

	/**
	 * Takes the name and returns base and extension
	 * Also renames to legal characters if second argument is true
	 */
	public static function parse_file_name( $name, $rename = false )
	{
		$name = basename($name);

		if( $rename ) {
			$name = preg_replace('/[^A-Za-z0-9.]/', '_', $name);
		}

		$name = explode('.', $name);
		$ext = strtolower(array_pop($name));
		$name = implode('.', $name);

		if( $rename ) {
			$name = trim(preg_replace('/(_)+/', '_', $name), '_');
			$name = substr($name, 0, 32);
		}

		return array( $name, $ext );
	}

	/**
	 * Format a date nicely
	 *
	 * @param  int  $time
	 * @param  int  $format
	 * @return string
	 */
	public static function date_string( $time, $format )
	{
		global $me;

		$gmt = gmmktime();

		$time += ( $me->tz * 3600 );
		$gmt  += ( $me->tz * 3600 );

		$datestr = date('M&\n\b\s\p;j,&\n\b\s\p;Y',$time);

		if( date('m/j/Y', $gmt) == date('m/j/Y', $time) ) {
			$datestr = 'Today';
		}
		else if( date('m/j/Y', $gmt-86400) == date('m/j/Y', $time) ) {
			$datestr = 'Yesterday';
		}
		else if( date('Y', $gmt) == date('Y', $time)) {
			$datestr = date('M&\n\b\s\p;j', $time);
		}
		if( $format == 1 ) { $datestr .= '&nbsp;at'; }
		else if( $format == 2 ) { $datestr .= ','; }

		$datestr .= date('&\n\b\s\p;g:i&\n\b\s\p;a', $time);

		return $datestr;
	}

}
