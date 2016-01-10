<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Static class for generic HTML elements
 *
 * @package com.geckowd;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v2.0$ 2008
 * @access public
 **/
class Gecko_HTML {
	/**
	 * Returns a JavaScript tag with data escaped
	 *
	 * @param string $code
	 * @param public static
	 * @return string
	 **/
	public static function getJavaScriptTag( $code ) {
		$return = "<script type=\"text/javascript\">\n";
		$return.= "//<![CDATA[\n";
		$return.= $code;
		$return.= "//]]>\n";
		$return.= "</script>";

		return $return;
	}

	/**
	 * Draws a image with the specified settings
	 *
	 * @param string The image source
	 * @param string The title of the image
	 * @param array Maxium Image dimensions (client rendering)
	 * @param array Additional Image Attributes
	 * @access public static
	 * @return string
	 **/
	public static function drawImg( $src, $title, $attrs = array() ) {
		$params = array();
		$params['src'] =  $src;
		$params['border'] = "0";
		$params['alt'] = $title;
		$params['width'] = $width;
		$params['height'] = $height;

		$params = array_merge( $params, $attrs );

		return self::constructTag( "img", $params );
	}

	/**
	 * Makes a new SWF Object tag
	 *
	 * @param string SWF Source
	 * @param string SWF Width
	 * @param string SWF Height
	 * @param string alignment
	 * @param string The color of the SWF
	 * @param string the Window mode
	 * @access public static
	 * @return string
	 **/
	public static function makeSWF( $swf, $width, $height, $align = 'center', $transcolor="#FFFFFF", $wmode = "transparent" ) {
		$html = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0\" width=\"$width\" height=\"$height\">
				<param name=\"movie\" value=\"$swf\" />
				<param name=\"quality\" value=\"high\" />
				<param name=\"bgcolor\" value=\"$transcolor\" />
				<param name=\"wmode\" value=\"$wmode\"/>
				<embed src=\"$swf\" quality=\"high\" wmode=\"$wmode\" width=\"$width\" height=\"$height\" type=\"application/x-shockwave-flash\" pluginspace=\"http://www.macromedia.com/go/getflashplayer\"></embed></object>";
		return $html;
	}

	/**
	 * Creates a new LinkTag
	 *
	 * @param string The Link label
	 * @param string The Link URL
	 * @param string The link target
	 * @access public static
	 * @return string
	 **/
	public static function LinkTag( $label, $url, $target = "_self", $moreParams = array()) {
		$params = array(
			"href" => $url,
			"target" => $target,
		);
		
		$params = array_merge($params, $moreParams);

		return self::constructTag( "a", $params, $label );
	}

	/**
	 * Inner function to create a HTML Tag
	 *
	 * @param string tag name
	 * @param array parameters
	 * @param string tag value
	 * @access private static
	 * @return string
	 **/
	private static function constructTag( $tag, $params, $value = "" ) {
		$buffer = "";

		foreach( $params as $param => $param_value ) {
			$buffer .= " " . $param . "=\"" . (string) $param_value . "\"";
		}

		if( !empty( $value ) ) {
			$out =  "<" . $tag .  $buffer . ">" . $value . "</" . $tag . ">";
		} else {
			$out =  "<" . $tag . $buffer . " />";
		}

		return $out;
	}
}
?>