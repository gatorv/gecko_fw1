<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Paginator object
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v2.0$ 2008
 * @access public
 **/
class Gecko_Paginator {
	private $_total; //@var private int Total number of results or records
	private $_per_page; //@var private int Number of results or records to display per pages
	private $_currPage; //@var private int Current Page in the dataset
	private $_separatorMode; //@var private int the Current Separator Mode
	private $_name; //@var private string the Name of the Paginator
	private $navSeparator = ""; //@var private string a Char or String used to separate pages
	private $next = "Siguiente &gt;"; //@var public string Text to use for next page
	private $prev = "&lt; Anterior"; //@var public string Text to use for previous page
	private $first = "&lt;&lt; Primero"; //@var public string Text to use for first page
	private $last = "Ultimo &gt;&gt;"; //@var public string Texto to use for last page
	private $maxpages = 30; //@var public int Maxium number of pages
	private $pageParam; //@var public string The Page param to pass

	const SEPARATOR_COMMA = 1; //@const Use a comma "," to separate pages
	const SEPARATOR_SPAN = 2; //@const Use a SPAN "<span></span>" To separate pages
	const SEPARATOR_CUSTOM = 3; //@const Use a Custom separator for each page

	/**
	 * Creates a new paginator object, and prepares things to build page.
	 *
	 * @param int $total Total number of records/objects
	 * @param int $per_page Number of records/objects to display, per page
	 * @param string $name Name of the paginator (optional)
	 * @access public
	 **/
	public function __construct($total, $per_page, $name="") {
		$this->_total = $total;
		$this->_per_page = $per_page;
		$this->_currPage = 0;
		$this->sepURI = "";
		$this->pageParam = "page";
		$this->_name = $name;
	}

	/**
	 * Sets the maxium number of links to display in the navigation
	 *
	 * @param int $number The number of pages
	 * @access public
	 **/
	public function setMaxPageLabels( $number ) {
		$this->maxpages = $number;
	}

	/**
	 * Sets the Labels for the navigation
	 *
	 * @param int $number The number of pages
	 * @access public
	 **/
	public function setLabels( $next, $previous, $first, $last ) {
		$this->next = $next;
		$this->prev = $previous;
		$this->first = $first;
		$this->last = $last;
	}

	/**
	 * Sets the Page Parameter to read the navigation
	 *
	 * @param int $number The number of pages
	 * @access public
	 **/
	public function setPageParam( $param ) {
		$this->pageParam = $param;
	}

	/**
	 * Establishes the separator string to use between pages
	 *
	 * @param string $sep Separator to use
	 * @return void
	 * @access public
	 **/
	public function setSeparator( $sep ) {
		$this->navSeparator = $sep;
	}

	/**
	 * Establishes the mode to use for separators, accepted values are:
	 * - SEPARATOR_COMMA
	 * - SEPARATOR_SPAN
	 * - SEPARATOR_CUSTOM
	 * All in the GeckoPaginator Class
	 *
	 * You must set the last option to see changes, set by setSeparator
	 *
	 * @see setSeparator
	 * @param int $mode The Mode to USe
	 * @access public
	 * @return void
	 **/
	public function setSeparatorMode( $mode ) {
		$this->_separatorMode = $mode;
	}

	/**
	 * Establishes the actual page, via a $_GET, or $_POST, or any other
	 * way, use it to set the current viewing page.
	 *
	 * @param int $page The actual Page
	 * @access public
	 * @return void
	 **/
	public function setCurrentPage( $page ) {
		$this->_currPage = $page;
	}

	/**
	 * This function returns the total number of pages
	 *
	 * @return int
	 * @access public
	 **/
	public function getTotalPages() {
		return $this->totalPages;
	}

	/**
	 * Returns the URI to a page
	 *
	 * @param int $page
	 * @param string $label
	 * @return string
	 * @access protected
	 **/
	protected function getUriLoc( $page, $label = "" ) {
		if( empty( $label ) ) $label = $page + 1;
		$page = (string) $page;

		$uri = Gecko_HTML::LinkTag( $label, Gecko_URL::getSelfURI( array( $this->pageParam => $page ) ) ) . "\n";

		return $uri;
	}

	/**
	 * This function calculates, and builds the navigation bit, and returns
	 * the navigation links
	 *
	 * @param bool $addPrevNext Add Previous and Next links?
	 * @param bool $addFirstLast Add First and Last Links?
	 * @access public
	 * @return string
	 **/
	public function getPages( $addPrevNext = false, $addFirstLast = false ) {
		$pages = (int) ceil( $this->_total / $this->_per_page );
		$this->totalPages = $pages;

		$out = array(); // Output Buffer of pages

		if( $addFirstLast ) {
			if( $this->_currPage > 0 ) {
				$out[] = $this->getUriLoc( "0", $this->first );
			}
		}

		if( $addPrevNext ) {
			if( $this->_currPage > 0 ) {
				$p = $this->_currPage - 1;
				$out[] = $this->getUriLoc( $p, $this->prev );
			}
		}

		if( $pages > $this->maxpages ) {
			$max = $this->maxpages;
			$curr = $this->_currPage;
			$pair1 = (int) floor( $max / 2 );
			$pair2 = (int) ceil( $max / 2 );

			if( ( $curr - $pair1 ) > 0 ) { // check if we are "FAR" from beginning
				$out[] = "...";
			}

			$start = ( $curr - $pair1 > 0 ) ? ( $curr - $pair1) : 0;
			$end = ( $curr + $pair2 < $pages ) ? ($curr + $pair2) : $pages;

			for( $i = $start; $i < $end; $i++ ) {
				if( $i == $this->_currPage ) {
					$out[] = "<span>" . ( $i + 1 ) . "</span>\n";
				} else {
					$out[] = $this->getUriLoc( $i );
				}
			}

			if( ( $curr + $pair2 ) < $pages ) { // check if we are "NEAR" from end
				$out[] = "...";
			}
		} else {
			for( $i = 0; $i < $pages; $i++ ) {
				if( $i == $this->_currPage ) {
					$out[] = "<span>" . ( $i + 1 ) . "</span>";
				} else {
					$out[] = $this->getUriLoc( $i );
				}
			}
		}

		$lastpage = $pages - 1;

		if( $addPrevNext ) {
			if( $this->_currPage < $lastpage ) {
				$p = $this->_currPage + 1;
				$out[] = $this->getUriLoc( $p, $this->next );
			}
		}

		if( $addFirstLast ) {
			if( $this->_currPage < $lastpage ) {
				$out[] = $this->getUriLoc( $lastpage, $this->last );
			}
		}

		switch( $this->_separatorMode ) {
		case self::SEPARATOR_COMMA:
		default:
			$links = implode( ", ", $out );
			break;
		case self::SEPARATOR_SPAN:
			$links = "<span>" . implode( "</span><span>", $out ) . "</span>";
			break;
		case self::SEPARATOR_CUSTOM:
			$links = implode( $this->navSeparator, $out );
			break;
		}

		return $links;
	}
}
?>