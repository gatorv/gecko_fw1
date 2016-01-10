<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once( 'Gecko/XML/Exception.php' );

/**
 * XML Generator
 *
 * @package gecko;
 * @author Christopher Valderrama <christopher@geckowd.com>
 * @copyright Copyright (c) 2006
 * @version $Id$v1.0$26 Oct 2006
 * @access public
 **/
class Gecko_XML {
	private $name;
	private $childs;
	private $attributes;
	private $data;
	private $addVer;
	private $version;
	private $encoding;

	/**
	 * Creates a new XML Document/Node
	 *
	 * If AddV is true, a XML Document is created, otherwise, a node is returned
	 *
	 * @param string $name
	 * @param boolean $addv
	 * @access public
	 **/
	public function __construct( $name, $addv = true ) {
		$this->name = $name;
		$this->childs = array();
		$this->attributes = array();
		$this->data = "";
		$this->addVersion = false;
		$this->version = "";
		if( $addv )
			$this->addVersion();
	}

	/**
	 * Adds the XML Version and encoding to a XML Document
	 *
	 * @param string $version XML Version
	 * @param string $encoding XML Encoding
	 * @access private
	 * @return void
	 **/
	public function addVersion( $version = "1.0", $encoding = "utf-8" ) {
		$this->version = "<?xml version=\"$version\" encoding=\"$encoding\" ?>";
		$this->addVer = true;
		$this->encoding = $encoding;
	}

	/**
	 * Creates a new Child Node for the current node/document
	 *
	 * @param string $name The Node Name
	 * @access public
	 * @return GeckoXML A new Node
	 **/
	public function createNode( $name ) {
		$node = new self( $name, false );
		$this->childs[] = $node;

		return $node;
	}

	/**
	 * Sets a attribute for this node
	 *
	 * @param string $name Attribute Name
	 * @param string $value Attribute Value
	 * @access public
	 * @return void
	 **/
	public function setAttribute( $name, $value ) {
		$this->attributes[$name] = $value;
	}

	/**
	 * From a key/value Array, it set's several attributes to a Node
	 *
	 * @param array $attributes The Attributes array
	 * @throws Exception
	 * @access public
	 * @return void
	 **/
	public function setAttributes( $attributes ) {
		if( !is_array( $attributes ) ) {
			throw new GeckoXMLException( '$attributes expected to be a array, ' . gettype( $attributes ) . ' given' );
		}

		foreach( $attributes as $name => $value ) {
			$this->setAttribute( $name, $value );
		}
	}

	/**
	 * Set's node inner data, it can cast Arrays, and objects
	 *
	 * @param mixed $data Node data
	 * @throws Exception when no cast is found
	 * @access public
	 * @return void
	 **/
	public function setData( $data ) {
		if( !is_string( $data ) ) {
			$data = $this->varToString( $data );
		}

		$this->data = "<![CDATA[" . $data . "]]>";
	}

	/**
	 * This function converts the object to a XML Representation
	 *
	 * @access public
	 * @return string
	 **/
	public function __toString() {
		$name = $this->name;
		$data = $this->data;
		$xml = "";
		$attrs = "";

		if( count( $this->attributes ) > 0 ) {
			foreach( $this->attributes as $aname => $avalue ) {
				$attrs .= " $aname=\"$avalue\"";
			}
		}

		if( empty( $data ) && ( count( $this->childs ) > 0 ) ) { // Node tag get childs
			$begintag = "<$name$attrs>";
			$endtag = "</$name>";
			foreach( $this->childs as $child ) {
				$xml .= (string) $child;
			}
			$xml = $begintag . $xml . $endtag;
		} else {
			if( empty( $data ) ) { // empty node
				$xml = "<$name$attrs />";
			} else {
				$xml = "<$name$attrs>$data</$name>";
			}
		}

		if( $this->addVer ) {
			$xml = $this->version . $xml;
		}

		return $xml;
	}

	/**
	 * Saves the XML Object to the Filesystem
	 *
	 * @param string $fname File Name
	 * @access public
	 * @throw Exception
	 * @return boolean
	 **/
	public function saveXML( $fname ) {
		$fh = @fopen( $fname, "w" );
		if( !$fh ) {
			throw new GeckoXMLException( "Unable to open " . $fname );
		}

		$rst = fwrite( $fh, $this->toString() );
		if( $rst === false ) {
			throw new GeckoXMLException( "Unable to write to " . $fname );
		}

		return fclose( $fh );
	}

	/**
	 * Writes the header, and streams the XML Document
	 *
	 * @access public
	 * @throw Exception
	 * @return void
	 **/
	public function streamXML() {
		if( headers_sent() ) {
			throw new GeckoXMLException( "Headers already sent!, check your code" );
		}

		$encoding = $this->encoding;
		header( "Content-type: text/xml; charset: $encoding" );
		echo $this->toString();
	}

	/**
	 * Converts a Variable to a String representation
	 *
	 * @param mixed $data
	 * @access private
	 * @return string
	 **/
	private function varToString( $data ) {
		if( is_array( $data ) ) {
			return print_r( $data, true );
		}

		if( is_object( $data ) ) {
			return get_object_vars( $data );
		}

		if( $data instanceof GeckoXML ) {
			return $data->toString();
		}

		throw new GeckoXMLException( '$data no conversor for this type of var: ' . gettype( $data ) );
	}
}
?>