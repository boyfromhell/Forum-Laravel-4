<?php
class Image
{
	public	$data,
			$size,
			$type,
			$width,
			$height,
			$max_width,
			$max_height,
			$max_size,
			$file,
			$extension;
	private $temp;

	function __construct( $data = null, $options = array() )
	{
		$this->data = $data;

		$this->size = $this->data['size'];
		$this->type = $this->data['type'];
		$this->temp = $this->data['tmp_name'];
		list( $this->width, $this->height ) = getimagesize($this->temp);
		
		if( isset($options['max_width']) ) {
			$this->max_width = $options['max_width'];
		}
		if( isset($options['max_height']) ) {
			$this->max_height = $options['max_height'];
		}
		if( isset($options['max_size']) ) {
			$this->max_size = $options['max_size'];
		}

		// Extensions
		if( $this->type == 'image/gif' ) {
			$this->extension = 'gif';
		}
		elseif( $this->type == 'image/png' ) {
			$this->extension = 'png';
		}
		elseif( $this->type == 'image/pjpeg' || $this->type == 'image/jpeg' ) {
			$this->extension = 'jpg';
		}
		else {
			throw new Exception('Only GIF, JPG, and PNG files are allowed');
		}
		
		// Max dimensions and size
		if( $this->width > $this->max_width || $this->height > $this->max_height ) {
			throw new Exception('Image is too large');
		}
		if( $this->size > $this->max_size ) {
			throw new Exception('Image is too large');
		}
	}

	// Overloaded set operator
	public function __set( $name, $value )
	{
		$this->$name = $value;
	}

	// Overloaded get operator
	public function __get( $name )
	{
		return $this->$name;
    }

	public function upload( $destination )
	{
		$this->file = $destination;
		return move_uploaded_file( $this->temp, ROOT . $destination );
	}
}
