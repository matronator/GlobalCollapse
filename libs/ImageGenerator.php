<?php

use Nette\Utils\Image,
	Nette\Utils\Strings,
	Nette\Utils\Validators;

/**
* Image generator
*/
class ImageGenerator
{

	/** @var string temp dir */
	protected $tempDir;

	/** @var string www dir */
	protected $wwwDir;

	/** @var string */
	protected $url;

	/** @var boolean */
	protected $crop;

	/** @var int wight thumbnail */
	protected $w;

	/** @var int height thumbnail */
	protected $h;


	/**
	* @param array $settings
	*/
	public function __construct($wwwDir)
	{
		$this->tempDir = '/upload/temp/'; //folder in $this->wwwDir;
		$this->wwwDir = $wwwDir;
	}


	// Create thumbnail with params
	public function getUrlThumb()
	{
		//distribution of the filename and extension
		$url = explode('/', $this->url);
		$url = explode('.', $url[count($url)-1]);

		//create new filename
		$filename = $url[0] . ($this->crop ? '-crop' : '') . '-' . (int)$this->w . '-' . (int)$this->h;
		$suffix = '.'.$url[1];
		$origFilename = $url[0] . $suffix;
		$thumbFilename = $filename . $suffix;

		//get absolute path
		$urlExplode = explode('www', $this->url);

		if (array_key_exists(1, $urlExplode)) {
			$sourcePathImage = $this->wwwDir.$urlExplode[1];
		}
		$returnUrl = ($_SERVER["REMOTE_ADDR"]=='127.0.0.1' || $_SERVER["REMOTE_ADDR"]=='::1' ? $urlExplode[0].'www'.$this->tempDir : $this->tempDir);


		if (strpos($this->url, 'www') == false) {
			$sourcePathImage = $this->wwwDir.$this->url;
		}

		//create temp folder and set permissions
		if (!file_exists($this->wwwDir.$this->tempDir)) {
			mkdir($this->wwwDir.$this->tempDir, 0777, true);
		}

		// If exist resize image
		if(file_exists($this->wwwDir.$this->tempDir.$thumbFilename)){
			if($_SERVER["REMOTE_ADDR"]=='127.0.0.1' || $_SERVER["REMOTE_ADDR"]=='::1'){
				return $returnUrl.$thumbFilename;
			}else{
				return $this->tempDir.$thumbFilename;
			}
		}

		// If exist source image
		if(!file_exists($sourcePathImage)){
			//if exist thumbnail with size
			if(!file_exists($this->wwwDir.$this->tempDir.'no-image-'.$this->w.'-'.$this->h.'.png')){
				$image = Image::fromBlank($this->w, $this->h, Image::rgb(255, 255, 255));
				$black = Image::rgb(0, 0, 0);
				$image->line(0, 1, $this->w, $this->h, $black);
				$image->line(0, $this->h, $this->w, 0, $black);
				$image->save($this->wwwDir.$this->tempDir.'no-image-'.$this->w.'-'.$this->h.'.png');
			}
			return $returnUrl.'no-image-'.$this->w.'-'.$this->h.'.png';
		}

		// Resizing and cropping image
		if($this->crop){
			//create thumbnail with exact dimensions
			$image = Image::fromFile($sourcePathImage);
			if( round($image->width/$image->height, 1)==round($this->w/$this->h, 1) ){
				$image->resize($this->w, $this->h);
			}else{
				$image->resize($this->w, $this->h, Image::FILL);
			}

			$image->crop('50%', '50%', $this->w, $this->h);
			$image->save($this->wwwDir.$this->tempDir.$thumbFilename);
		}else{
			//Resizing image and create thumbnail
			$image = Image::fromFile($sourcePathImage);
			$image->resize($this->w, $this->h);
			$image->save($this->wwwDir.$this->tempDir.$thumbFilename);
		}

		return $returnUrl.$thumbFilename;
	}



	/**
	* Set crop image
	* @param  boolean
	* @return self
	*/
	public function setCropImage($crop)
	{
		$this->crop = $crop;
		return $this;
	}

	/**
	* Set width thumbnail
	* @param  int
	* @return self
	*/
	public function setWidth($w)
	{
		$this->w = $w;
		return $this;
	}

	/**
	* Set height thumbnail
	* @param  int
	* @return self
	*/
	public function setHeight($h)
	{
		$this->h = $h;
		return $this;
	}

	/**
	* Set url
	* @param  string
	* @return self
	*/
	public function setUrl($url)
	{
		if(substr($url, -1)=='/'){
			$this->url = $url.'/not-found.jpg';
		}else{
			$this->url = $url;
		}
		return $this;
	}


	public function downloadImageFromUrl($url, $path)
	{
		$ch = curl_init($url);
		$fp = fopen($path, 'wb');

		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}

}
