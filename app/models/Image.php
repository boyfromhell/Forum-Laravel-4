<?php

class Image
{

	/**
	 * Full path of original photo
	 *
	 * @param  string
	 */
	protected $path;

	/**
	 * Folder of original photo
	 *
	 * @param  string
	 */
	protected $folder;

	/**
	 * Base file name before extension
	 *
	 * @param  string
	 */
	protected $name;

	/**
	 * Base file name of scaled version
	 *
	 * @param  string
	 */
	protected $newname;

	/**
	 * Extension
	 *
	 * @param  string
	 */
	protected $ext;

	/**
	 * New extension for scaled version
	 *
	 * @param  string
	 */
	protected $newext;

	/**
	 * Image object generated from original file
	 *
	 * @param  Image
	 */
	protected $src = NULL;

	/**
	 * Image object created for scaled versions
	 *
	 * @param  Image
	 */
	protected $dest = NULL;

	/**
	 * Width
	 *
	 * @param  int
	 */
	protected $width;

	/**
	 * Height
	 *
	 * @param  int
	 */
	protected $height;

	/**
	 * Ratio (width over height)
	 *
	 * @param  float
	 */
	protected $ratio;

	/**
 	 * Create a new photo
	 *
	 * @param  string  $path  Path to file
	 */
	public function __construct( $path )
	{
		$this->path = $path;

		$info = pathinfo($path);

		$this->folder = $info['dirname'];
		$this->name = $info['filename'];
		$this->ext = strtolower($info['extension']);

		if( $this->ext == 'jpeg' ) { $this->ext = 'jpg'; }
		$this->newname = $this->name;
		$this->newext = $this->ext;
	}

	/**
	 * Create image from source
	 *
	 * @return void
	 */
	protected function create()
	{
		if( $this->src ) { return; }

		switch( $this->ext ) {
			case 'gif':
				$this->src = ImageCreateFromGif($this->path);
				break;
			case 'png':
				$this->src = ImageCreateFromPng($this->path);
				break;
			case 'jpg':
				$this->src = ImageCreateFromJpeg($this->path);
				break;
			default:
				throw new Exception('Unknown file type: '.$this->ext);
				break;
		}

		$this->width = ImageSx($this->src);
		$this->height = ImageSy($this->src);
		$this->ratio = ( $this->width / $this->height );
	}

	/**
	 * Generate a scaled image based on current dimensions
	 *
	 * @param  int  $nw  New width to scale to
	 * @param  int  $nh  New height to scale to
	 * @param  string  $mode  Mode of scaling: constrain, crop, width, height
	 * @return Image
	 */
	public function scale($nw, $nh, $mode = 'constrain')
	{
		$this->create();

		switch( $mode ) {
			// Width and height are max, scale proportionately to fit
			case 'constrain':
				$ideal = $nw / $nh;

				if( $this->ratio > $ideal ) {
					$nh = (int)( $nw / $this->ratio );
				}
				else {
					$nw = (int)( $nh * $this->ratio );
				}
				break;

			// Width and height are exact
			case 'crop':
				break;

			// Width is exact
			case 'width':
				$nh = (int)( $nw / $this->ratio );
				break;

			// Height is exact
			case 'height':
				$nw = (int)( $nh * $this->ratio );
				break;

			default:
				throw new Exception('Unknown scale mode: '.$mode);
				break;
		}

		// If it's already smaller, don't do anything
		if( $this->width <= $nw && $this->height <= $nh ) {
			$nw = $this->width;
			$nh = $this->height;
		}

		$this->dest = ImageCreateTrueColor($nw, $nh);
		ImageCopyResampled(
			$this->dest, $this->src,
			0, 0, 0, 0,
			$nw, $nh, $this->width, $this->height
		);

		// Use this as the new image to scale from
		// Saves memory when scaling down multiple sizes
		//@ImageDestroy($this->src);
		$this->src = $this->est;
		$this->width = $nw;
		$this->height = $nh;
		$this->ratio = ( $this->width / $this->height );

		return $this;
	}

	/**
	 * Scale and crop to exact dimensions
	 *
	 * @param  int  $width
	 * @param  int  $height
	 * @return Image
	 */
	public function scaleCrop($width, $height)
	{
		return $this->scale($width, $height, 'crop');
	}

	/**
	 * Scale based on width
	 *
	 * @param  int  $width
	 * @return Image
	 */
	public function scaleWidth($width)
	{
		return $this->scale($width, NULL, 'width');
	}

	/**
	 * Scale based on height
	 *
	 * @param  int  $height
	 * @return Image
	 */
	public function scaleHeight($height)
	{
		return $this->scale(NULL, $height, 'height');
	}

	/**
	 * Scale based on longer side
	 *
	 * @param  int  $dimension
	 * @return Image
	 */
	public function scaleLong($dimension)
	{
		return $this->scale($dimension, $dimension);
	}

	/**
	 * Set suffix of new scaled file
	 *
	 * @param  string  $suffix
	 * @return Image
	 */
	public function setSuffix( $suffix )
	{
		$this->newname = $this->name . $suffix;
		return $this;
	}

	/**
	 * Set name of new scaled file
	 *
	 * @param  string  $filename  File name not including extension
	 * @return Image
	 */
	public function setFilename( $filename )
	{
		$this->newname = $filename;
		return $this;
	}

	/**
	 * Save whatever current actions have been applied into a new file
	 * using the same format as the original file
	 *
	 * @param  int  $quality
	 * @return Image
	 */
	public function save( $quality = 94 )
	{
		switch( $this->ext ) {
			case 'jpg':
				return $this->saveJpg($quality);
				break;

			case 'gif':
				return $this->saveGif();
				break;

			case 'png':
				return $this->savePng();
				break;
		}
	}

	/**
	 * Save as a JPG
	 *
	 * @param  int  $quality
	 * @return Image
	 */
	public function saveJpg( $quality = 94 )
	{
		$this->newext = 'jpg';

		ImageJpeg($this->dest, $this->folder.'/'.$this->newname.'.jpg', $quality);

		return $this;
	}

	/**
	 * Save as a PNG
	 *
	 * @return Image
	 */
	public function savePng()
	{
		$this->newext = 'png';

		ImagePng($this->dest, $this->folder.'/'.$this->newname.'.png');

		return $this;
	}

	/**
	 * Save as a GIF
	 *
	 * @return Image
	 */
	public function saveGif()
	{
		$this->newext = 'gif';

		ImageGif($this->dest, $this->folder.'/'.$this->newname.'.gif');

		return $this;
	}

	/**
	 * Upload to S3
	 *
	 * @param  string  $folder  Folder to upload to
	 * @param  bool  $public  ACL setting
	 * @return Image
	 */
	public function pushToS3( $folder, $public = true )
	{
		if( ! $folder ) {
			throw new Exception('Please specify an S3 folder');
		}

		if( Helpers::push_to_s3(
			$this->folder.'/'.$this->newname.'.'.$this->newext,
			$folder.'/'.$this->newname.'.'.$this->newext,
			$public
		) ) {
			/*if( $this->newname.'.'.$this->newext != $this->name.'.'.$this->ext ) {
				unlink($this->folder.'/'.$this->newname.'.'.$this->newext);
			}*/
		}

		return $this;
	}

	/**
	 * Unlink original file from this system
	 *
	 * @return Image
	 */
	public function unlink()
	{
		//@ImageDestroy($this->src);
		//@ImageDestroy($this->dest);
		unlink($this->path);
	}

}

