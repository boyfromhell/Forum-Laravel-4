<?php namespace Parangi;

class Helpers
{

	/**
	 * Adjust timezone and format date
	 *
	 * @param  string  $format
	 * @param  mixed  $time
	 * @return string
	 */
	public static function local_date( $format, $time )
	{
		global $me;

		if( ! is_numeric($time) ) {
			$time = strtotime($time);
		}

		$time += ( $me->timezone * 3600 );

		return date($format, $time);
	}

	/**
	 * Takes the name and returns base and extension
	 * Also renames to legal characters if second argument is true
	 *
	 * @param  string  $name
	 * @param  bool    $rename
	 * @return array
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
	 * Convert bytes to a user-friendly size
	 *
	 * @param  string  $file  path to file
	 * @return string
	 */
	public static function english_size( $file )
	{
		if( ! file_exists($file) ) {
			return 0;
		}

		$bytes = filesize($file);
		$units = array('bytes', 'kb', 'MB', 'GB');
		$counter = 0;
		
		while( $bytes >= 1024 ) {
			$bytes /= 1024;
			$counter++;
		}

		return( number_format($bytes, 2) . ' ' . $units[$counter] );
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

		if( ! is_numeric($time) ) {
			$time = strtotime($time);
		}

		$gmt = gmmktime();

		$time += ( $me->timezone * 3600 );
		$gmt  += ( $me->timezone * 3600 );

		$datestr = date('M j, Y',$time);

		if( date('m/j/Y', $gmt) == date('m/j/Y', $time) ) {
			$datestr = 'Today';
		}
		else if( date('m/j/Y', $gmt-86400) == date('m/j/Y', $time) ) {
			$datestr = 'Yesterday';
		}
		else if( date('Y', $gmt) == date('Y', $time)) {
			$datestr = date('M j', $time);
		}
		if( $format == 1 ) { $datestr .= ' at'; }
		else if( $format == 2 ) { $datestr .= ','; }

		$datestr .= date(' g:i a', $time);

		return $datestr;
	}

	/**
	 * Upload a file to S3
	 *
	 * @param  string  $local_path  Absolute path to file
	 * @param  string  $s3_path  Remote path including folder and filename
	 * @param  bool  $public  ACL setting
	 * @return bool  Success
	 */
	public static function push_to_s3( $local_path, $s3_path, $public = false )
	{
		if( ! Config::get('services.aws.enabled') ) {
			return false;
		}

		$s3 = new S3(
			Config::get('services.aws.access_key'),
			Config::get('services.aws.secret_key')
		);

		$acl = ( $public ? S3::ACL_PUBLIC_READ : S3::ACL_AUTHENTICATED_READ );
		$headers = array(
			'Cache-Control' => 'public, max-age=31536000'
		);

		return $s3->putObjectFile(
			$local_path,                        // path on server
			Config::get('services.aws.bucket'), // bucket
			$s3_path,                           // path on S3
			$acl,                               // ACL settings
			array(),                            // meta headers
			$headers                            // request headers
		);
	}

	/**
	 * Delete a file from S3
	 *
	 * @param  string  $s3_path  Remote path
	 * @return bool  Success
	 */
	public static function delete_from_s3( $s3_path )
	{
		if( ! Config::get('services.aws.enabled') ) {
			return false;
		}

		$s3 = new S3(
			Config::get('services.aws.access_key'),
			Config::get('services.aws.secret_key')
		);

		return $s3->deleteObject(
			Config::get('services.aws.bucket'), // bucket
			$s3_path                            // path on S3
		);
	}

	/**
	 * Generate a group of radio buttons
	 *
	 * @return string
	 */
	public function radioGroup( $name, $options, $default = NULL, $extra_class )
	{
		if( Input::old($name) !== NULL ) {
			$default = Input::old($name);
		}

		$html = '<div class="btn-group '.$extra_class.'" data-toggle="buttons">';

		foreach( $options as $value => $label )
		{
			$html .= '<label class="btn btn-default';
			if( $default == $value ) { $html .= ' active'; }
			$html .= '">'."\n";

			$html .= Form::radio($name, $value, ( $default == $value )) . "\n";
			$html .= $label."\n";

			$html .= '</label>'."\n";
		}

		$html .= '</div>'."\n";

		return $html;
	}

}

