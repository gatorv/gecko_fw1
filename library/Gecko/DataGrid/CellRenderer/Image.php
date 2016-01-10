<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataGrid/CellRenderer/Interface.php');

/**
 * Image Renderer for cells
 *
 * @package Gecko.DataGrid.CellRenderer;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataGrid_CellRenderer_Image extends Gecko_DataGrid_CellRenderer {
	/**
	 * The URL to add to the image (optional)
	 *
	 * @var string
	 */
	private $uriDir;
	/**
	 * The FileSystem dir to the image for
	 * checking if it exists (optional)
	 *
	 * @var string
	 */
	private $fsDir;
	/**
	 * The not Found image in case of 404
	 * (optional)
	 *
	 * @var string
	 */
	private $notFoundImage;
	/**
	 * Check if the image exists before
	 * rendering
	 *
	 * @var boolean
	 */
	private $checkImageLoc = false;

	/**
	 * Creates a new renderer for images, it can
	 * check if the image exist if you suply
	 * the fs location.
	 *
	 * It can also add a path to the image before
	 * rendering the image
	 *
	 * @param string The URL to the image (optional)
	 * @param string The FileSystem dir to the image (optional)
	 * @param string The not found image
	 */
	public function __construct($urlDir = '', $fsDir = '', $notFound = '') {
		if(!empty($urlDir) && ( substr($urlDir, -1) != '/' )) {
			$urlDir .= '/';
		}

		$this->uriDir = $urlDir;
		if(!empty($fsDir) && is_dir($fsDir)) {
			if(substr($fsDir, -1) != "/") {
				$fsDir .= "/";
			}
			$this->fsDir = $fsDir;
			$this->notFoundImage = $notFound;
			$this->checkImageLoc = true;
		}
	}

	/**
	 * Renders the value using the specified settings
	 *
	 * @param string The cell value
	 */
	public function renderValue($value) {
		$image = $this->uriDir . $value;

		if($this->checkImageLoc == true) {
			$path = $this->fsDir . $value;
			if(!file_exists($path)) {
				$image = $this->notFoundImage;
			}
		}

		return Gecko_HTML::drawImg( $image, $value );
	}
}
?>