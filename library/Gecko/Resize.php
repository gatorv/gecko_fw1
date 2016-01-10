<?php
class Gecko_Resize
{
	private $_sFileName;
	private $_nType;
	private $_gdResource;
	private $_nWidth;
	private $_nHeight;
	private $_nMaxWidth;
	private $_nMaxHeight;
	private $_imageFunction;
	
	public function __construct($sFileName)
	{
		if (!function_exists("gd_info")) {
			throw new Gecko_Exception('GD Library it\'s not detected');
		}
		
		if (!file_exists($sFileName)) {
			throw new Gecko_Exception("$sFileName not found");
		}
		
		if (!is_readable($sFileName)) {
			throw new Gecko_Exception("$sFileName not readable");
		}
		
		$this->_sFileName = $sFileName;
		$aInfo = @getimagesize($sFileName);
		$this->_nWidth = (int) $aInfo[0];
		$this->_nHeight = (int) $aInfo[1];
		$this->_nType = $aInfo[2];
		switch ($this->_nType) {
			case IMAGETYPE_JPEG:
			case IMAGETYPE_JPEG2000:
				$this->_gdResource = imagecreatefromjpeg($sFileName);
				$this->_imageFunction = 'imagejpeg';
				break;
			case IMAGETYPE_GIF:
				$this->_gdResource = imagecreatefromgif($sFileName);
				$this->_imageFunction = 'imagegif';
				break;
			case IMAGETYPE_PNG:
				$this->_gdResource = imagecreatefrompng($sFileName);
				$this->_imageFunction = 'imagepng';
				break;
			default:
				throw new Gecko_Exception('Invalid image type: ' . image_type_to_mime_type($this->_nType));
				break;
		}
	}
	
	public function resizeTo($nWidth, $nHeight)
	{
		$this->_nMaxWidth = $nWidth;
		$this->_nMaxHeight = $nHeight;
		
		$aDimensions = $this->_calcNewDimensions($this->_nWidth, $this->_nHeight);
		$newWidth = $aDimensions['newWidth'];
		$newHeight = $aDimensions['newHeight'];
		if(function_exists('imagecreatetruecolor')) {
			$newImage = imagecreatetruecolor($newWidth,$newHeight);
		} else {
			$newImage = imagecreate($newWidth,newHeight);
		}
		
		ImageCopyResampled(
			$newImage,
			$this->_gdResource,
			0,
			0,
			0,
			0,
			$newWidth,
			$newHeight,
			$this->_nWidth,
			$this->_nHeight
		);
		
		$this->_gdResource = $newImage;
	}
	
	public function save()
	{
		$imageFunction = $this->_imageFunction;
		$imageFunction($this->_gdResource, $this->_sFileName);
	}
	
	public function stream()
	{
		if (headers_sent()) {
			throw new Exceptions('Headers already sent!');
		}
		
		header('Content-type: ' . image_type_to_mime_type($this->_nType));
		$imageFunction = $this->_imageFunction;
		$imageFunction($this->_gdResource, $this->_sFileName);
	}
	
	private function _calcNewDimensions($width, $height)
	{
		$newSize = array('newWidth'=>$width,'newHeight'=>$height);
		
        if($this->_nMaxWidth > 0) {
            $newSize = $this->_calcWidth($width, $height);
            if($this->_nMaxHeight > 0 && $newSize['newHeight'] > $this->_nMaxHeight) {
                $newSize = $this->_calcHeight($newSize['newWidth'],$newSize['newHeight']);
            }
        }

        if($this->_nMaxHeight > 0) {
            $newSize = $this->_calcHeight($width,$height);
            if($this->_nMaxWidth > 0 && $newSize['newWidth'] > $this->_nMaxWidth) {
                $newSize = $this->_calcWidth($newSize['newWidth'],$newSize['newHeight']);
            }
        }

        return $newSize;
	}
	
	private function _calcWidth($width, $height)
	{
		$newWp = (100 * $this->_nMaxWidth) / $width;
        $newHeight = ($height * $newWp) / 100;
        return array('newWidth'=>intval($this->_nMaxWidth),'newHeight'=>intval($newHeight));
	}
	
	private function _calcHeight($width, $height)
	{
		$newHp = (100 * $this->_nMaxHeight) / $height;
        $newWidth = ($width * $newHp) / 100;
        return array('newWidth'=>intval($newWidth),'newHeight'=>intval($this->_nMaxHeight));
	}
}