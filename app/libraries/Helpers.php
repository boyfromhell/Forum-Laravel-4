<?php namespace Parangi;

use Config;
use Form;
use Input;
use S3;

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
		if (is_numeric($file)) {
			$bytes = $file;
		} else {
			if (! file_exists($file)) {
				return 0;
			}

			$bytes = filesize($file);
		}

		$units = array('bytes', 'kb', 'MB', 'GB');
		$counter = 0;
		
		while ($bytes >= 1024) {
			$bytes /= 1024;
			$counter++;
		}

		return number_format($bytes, 2) . ' ' . $units[$counter];
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
	 * @param  bool  $unlink  Delete local file after successful upload
	 * @return bool  Success
	 */
	public static function push_to_s3( $local_path, $s3_path, $public = false, $unlink = true )
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

		$success = $s3->putObjectFile(
			$local_path,                        // path on server
			Config::get('services.aws.bucket'), // bucket
			$s3_path,                           // path on S3
			$acl,                               // ACL settings
			array(),                            // meta headers
			$headers                            // request headers
		);

		if ($success && $unlink) {
			unlink($local_path);
		}

		return $success;
	}

	/**
	 * Delete a file from S3
	 *
	 * @param  string  $s3_path  Remote path
	 * @return bool  Success
	 */
	public static function delete_from_s3($s3_path)
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

	/**
	 * Check if user has mobile enabled/disabled; default to detecting user agent
	 */
	public static function is_mobile()
	{
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		
		// Manual overrides
		if (isset($_GET['mobile'])) {
			return true;
		} else if (isset($_GET['no_mobile'])) {
			return false;
		} else if (isset($_COOKIE['mobile'])) {
			return true;
		} else if (isset($_COOKIE['no_mobile'])) {
			return false;
		}
		// Don't count iPad
		if ((bool)strpos($useragent,'iPad')) {
			return false;
		}
		
		if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
			return true;
		}
		return false;
	}

}

