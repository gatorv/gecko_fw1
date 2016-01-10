<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataGrid/Exception.php');
require_once('Gecko/DataGrid/Formatter/Table.php');

/**
 * Web Grid that draws data in a table
 * from a data source
 *
 * @package Gecko;
 * @author Christopher Valderrama <christopher@geckowd.com>
 * @access public
 */
class Gecko_DataGrid {
	/**
	 * Grid Properties
	 **/
	private $name; //@var private The Table Name
	private $links; //@var private The HTML Links for paginating @See Gecko_Paginator
	private $sHeaders = true; //@var private Boolean indicating to show or not the headers
	private $color1 = ""; //@var private for enabling cell coloring
	private $color2 = ""; //@var private Color 2
	private $customcolors = false; //@var private Use custom colors
	private $theme = "default"; //@var private Theme name to use (CSS)
	private $noRecords = "No records found"; //@var private The No Records found Message
	private $parsed = false; //@var private Boolean to indicate whether the table has been built
	private $skipColumns = array(); //@var private Array With columns names to skip from rendering

	/**
	 * Table Formatter
	 **/
	private $formatter = null; //@var private Gecko_DataGrid_Formatter_Interface used to format the Table Headers and Columns

	/**
	 * Table Model
	 **/
	private $model = null; //@var private Gecko_DataSource_Table_Interface Model used to pull table data

	/**
	 * Table paginator
	 **/
	private $paginator = null; //@var private Gecko_Paginator object to calculate pages

	/**
	 * Javascript Events for row click / double click
	 **/
	private $onClick = ""; //@var private Fire a Javascript Event upon clickng a Row
	private $onClickParams = array(); //@var private String or Array with the field names to send to the Javascript Event
	private $ondblClick = ""; //@var private Fire a Javascript Event upon double clicking a Row
	private $ondblClickParams = array(); //@var private String or Array with the field names to send to the event

	/**
	 * Sorting table options
	 **/
	private $sorting = false; //@var private Boolean indicating enable or not the table sorting (via SQL)
	private $sortcol = ""; //@var private The column to sort
	private $sortorder = "ASC"; //@var private Sort Ascendant or Descendant.
	private $sortparam = ""; //@var private The parameter name to indicate the sort column.
	private $sortorderparam = ""; //@var private The parameter name to indicate the sort order
	private $supi; //@var private The SRC of the image for ASC sorting
	private $sdni; //@var private The SRC of the image for DESC sorting
	private $noSortColumns = array(); //@var private Array with columns that are unsorteable

	/**
	 * Pagination table options
	 **/
	private $paginate = false; //@var private Boolean indicating to paginate or not the results.
	private $start = 0; //@var private Number indicating where to start in the results.
	private $maxrows = 30; //@var private Number indicating the maxium number or rows to return.
	private $totalrows = 0; //@var private Number indicating the TOTAL number of rows;
	private $navsep = ", "; //@var private Separator for Navigation
	private $navparam = ""; //@var private Parameter for Navigation
	private $navmsg = "Showing %s of %s pages"; //@var private String for Navigation Message
	
	/**
     * Translation object
     * @var Zend_Translate
     */
    protected $_translator;

    /**
     * Default translation object for all validate objects
     * @var Zend_Translate
     */
    protected static $_defaultTranslator;

    /**
     * Is translation disabled?
     * @var Boolean
     */
    protected $_translatorDisabled = false;

	/**
	 * Creates a Grid from a Zend_Db_Table_Abstract
	 *
	 * @param string $name The name of the grid
	 * @param Zend_Db_Table_Abstract $table The Table adapter to put
	 * @param array $columns The Columns to set to the grid
	 * @return Gecko_DataGrid The grid pre-populated
	 */
	public static function createFromDbTable($name, Zend_Db_Table_Abstract $table, $columns = array()) {
		require_once('Gecko/DataSource/Table/DBTable.php');
		$model = new Gecko_DataSource_Table_DBTable($table);

		$grid = new self($name, $model);
		return $grid;
	}

	/**
	 * Creates a Grid from a SQL Query (or Zend_Db_Select), if you
	 * don't submit a Db Adapter it will try to create from Gecko_DB(Singleton),
	 * or it will look for a 'db' entry in Zend_Registry
	 *
	 * @param string $name The table name
	 * @param string|Zend_Db_Select $sql
	 * @param Zend_Db_Adapter_Abstract $db
	 * @return Gecko_DataGrid The grid pre-populated
	 */
	public static function createFromSQL($name, $sql, $db = null) {
		if( $db === null ) { // Try to create
			try {
				$db = Gecko_DB::getInstance();
			} catch( Zend_Db_Exception $zde ) {
				if(Zend_Registry::isRegistered('db')) {
					$db = Zend_Registry::get('db');
				}
			}

			if( $db === null ) { // DB still not set Throw a error
				throw new Gecko_DataGrid_Exception('$db it\'s not set, check the documentation');
			}
		}

		/** Check if it's a select object **/
		if($sql instanceof Zend_Db_Select) {
			$sql = $sql->__toString();
		}

		require_once('Gecko/DataSource/Table/SQL.php');
		$model = new Gecko_DataSource_Table_SQL($sql, $db);
		$grid = new self($name, $model);

		return $grid;
	}

	/**
	 * Constructor, initiates a new DataGrid
	 *
	 * @param string $tablename The table name
	 * @param Gecko_DataSource_Table_Interface $model The table Model to fetch data
	 * @param array $settings The Settings of the table
	 * @param Gecko_DataGrid_Formatter_Interface $formatter The formatter of the table
	 **/
	public function __construct($tablename, Gecko_DataSource_Table_Interface $model, $settings = array(), Gecko_DataGrid_Formatter_Interface $formatter = null) {
		// Setup Basic grid stuff
		$this->supi = '/images/up.png';
		$this->sdni = '/images/dn.png';
		$this->name = $tablename;

		$this->sortparam = $tablename . "Sort";
		$this->sortorderparam = $tablename . "Order";
		$this->navparam = $tablename . "Page";

		// Parse settings if available
		if (count($settings) > 0) {
			$this->parseSettings($settings);
		}

		// save model
		$this->model = $model;

		// setup table formatter if not set
		if( $formatter == null && $this->formatter == null ) {
			$this->formatter = new Gecko_DataGrid_Formatter_Table();
			$this->formatter->setGrid($this);
		}
		
		if ($formatter instanceof Gecko_DataGrid_Formatter_Interface) {
			$this->setFormatter($formatter);
		}
	}

	/**
	 * This method parses the settings array (if exists), and populates grid properties
	 * in a quick easy way.
	 *
	 * @param array $settings The Settings Array
	 * @access private
	 * @return void
	 */
	private function parseSettings($settings) {
		foreach($settings as $section => $data) {
			if ($section == 'theme') {
				$this->setTheme($data);
			}

            if (($section == 'sortArrows') && (is_array($data))) {
                $this->setSortArrows($data[0], $data[1]);
            }

            if ($section == "differentColors" ) {
            	$this->colorizeRows($data[0], $data[1]);
            }

            if ($section == 'showHeaders') {
                $this->showHeaders($data);
            }

            if ($section == 'sorting') {
                $this->setSorting($data['sortColumn'], $data['sortOrder']);
                if (!empty($data['sortParam'])) $this->setSortParam($data['sortParam']);
                if (!empty($data['orderParam'])) $this->setOrderParam($data['orderParam']);
                if (!empty($data['noSortColumns'])) $this->noSortColumns = $data['noSortColumns'];
            }

            if ($section == 'paginate') {
                $this->setMaxRows($data);
            }

            if ($section == 'navMsg') {
                $this->setNavMsg($data);
            }

            if ($section == 'noRecords') {
                $this->noRecords = $data;
            }

            if ($section == 'skipColumns' ) {
				$this->skipColumns($data);
            }

            if ($section == 'formatter' ) {
            	$this->setFormatter($data);
            }
        }
    }

    /**
     * Sets wich columns should be hidden from the grid
     *
     * @param array $columns The columns to skip
     */
	public function skipColumns($columns) {
		if(!is_array($columns)) {
			throw new Gecko_DataGrid_Exception('$columns expected to be an array, ' . gettype( $columns ) . ' given' );
		}

		$this->skipColumns = $columns;
	}

	/**
	 * Sets the CSS Theme to draw the DataGrid
	 *
	 * @param string $themeName The Theme Name
	 * @return void
	 */
	public function setTheme($themeName) {
		$this->theme = $themeName;
	}

	/**
	 * Sets the Arrows to use to sort the headers
	 *
	 * @param string $up_arrow The URL to the Sort Asc Image
	 * @param string $down_arrow The URL to the Sort Desc Image
	 * @return void
	 */
	public function setSortArrows($up_arrow, $down_arrow) {
		$this->supi = $down_arrow;
		$this->sdni = $up_arrow;
	}

	/**
	 * This function will set a boolean to show or hide the headers
	 *
	 * @param boolean $show
	 * @return boolean
	 */
	public function showHeaders($show = true) {
		$this->sHeaders = $show;
		return $show;
	}

	/**
	 * Set the row colors, you can specify a class name or a RGB value
	 *
	 * @param string $color1 The Color for even rows
	 * @param string $color2 The Color for odd rows
	 * @return void
	 */
	public function colorizeRows($color1, $color2) {
		$this->color1 = $color1;
		$this->color2 = $color2;
		$this->customcolors = true;
	}

    /**
     * Enable DataGrid Sorting
     *
     * @param string $sortcol The Sort Column
     * @param string $sortorder The Sort Order (defaults to ASC)
     * @return
     */
    public function setSorting($sortcol = '', $sortorder = 'ASC') {
        $this->sorting = true;
        $this->sortcol = $sortcol;
        $this->sortorder = $sortorder;
    }

    /**
     * Sets the Sort Parameter
     *
     * @param string $param
     * @return void
     */
    public function setSortParam($param) {
        if (empty($param) || !is_string($param)) {
            throw new Gecko_DataGrid_Exception("param must be set to a non-empty string");
        }

        $this->sortparam = $param;
    }

    /**
     * Sets the Order Parameter
     *
     * @param string $param
     * @return
     */
    public function setOrderParam($param) {
    	if (empty($param) || !is_string($param)) {
            throw new Gecko_DataGrid_Exception("param must be set to a non-empty string");
        }
        $this->sortorderparam = $param;
    }

    /**
     * Sets the max number of rows to show
     *
     * @param int $num The maxium number of rows
     * @return void
     */
    public function setMaxRows($num) {
        $this->maxrows = $num;
        $this->start = 0;
        $this->paginate = true;
    }

    /**
     * Sets the Javascript action to fire upon a row click,
     * you can send a array of field names to send all field names
     *
     * @param string $action The Javascript Action
     * @param mixed $send_params The params to send, can be a Array of Params
     * @return
     */
    public function setOnClick($action, $send_params) {
        $this->onClick = $action;
        $this->onClickParams = $send_params;
    }

    /**
     * Sets the Javascript action to fire upon a double click in a row,
     * you can send a array of field names
     *
     * @param string $action The Javascript action
     * @param mixed $send_params The params to send, can be a Array of Params
     * @return
     */
    public function setOnDblClick($action, $send_params) {
        $this->ondblClick = $action;
        $this->ondblClickParams = $send_params;
    }

    /**
     * Sets the DataGrid navigation message, use %s as place holders
     * For example:
     *
     * Viewing page %s of %s
     *
     * @param string $msg
     * @return void
     */
    public function setNavMsg($msg) {
        $this->navmsg = $msg;
    }

    /**
     * Returns the parsed Navigation
     *
     * @return
     */
    public function getNavMsg() {
        return $this->navmsg;
    }

	/**
	 * Returns the current page
	 *
	 * @return string
	 */
	public function getCurrentPage() {
		$page = Gecko_URL::getRequestParam($this->navparam);
		return ( empty($page) ? 0 : (int) $page );
	}

	/**
	 * Returns the Paginator object
	 *
	 * @return Gecko_Paginate object
	 **/
	public function getPaginator() {
		return $this->paginator;
	}
	
	/**
	 * Returns the Nav Parameter
	 * 
	 * @return string
	 */
	public function getNavParam() {
		return $this->navparam;
	}

	/**
	 * Sets the GridFormatter
	 *
	 * @param Gecko_DataGrid_Formatter_Interface $formatter The formatter to set
	 */
	public function setFormatter(Gecko_DataGrid_Formatter_Interface $formatter) {
		$this->formatter = $formatter;
		$this->formatter->setGrid($this);
	}
    /**
     * Returns the Formatter used in the table
     *
     * @return Gecko_DataGrid_Formatter_Interface The Grid Formatter
     */
    public function getFormatter() {
        return $this->formatter;
    }

    /**
     * Returns the Output of the Table
     *
     * @return string The Table constructed
     */
    public function getOutput() {
		if ($this->parsed) {
			return $this->formatter->__toString();
        } else {
			throw new Gecko_DataGrid_Exception("Table isn't build");
        }
	}

	/**
     * Returns the Output of the Table
     *
     * @return string The Table constructed
     */
	public function __toString() {
		if( $this->parsed ) {
			return $this->formatter->__toString();
		}

		return "";
	}

    /**
     * This function will return the ID of the table
     *
     * @return string the Id of the Table
     */
    public function getId() {
        return $this->name;
    }

    /**
     * Returns the DataSource of the grid
     *
     * @return Gecko_DataSource_Table_Interface The Grid's DataSource
     */
    public function getDataSource() {
        return $this->model;
    }

    /**
     * This method will generate the DataGrid
     *
     * @return boolean
     */
    public function buildTable() {
        /**
         * Setup basic model settings
         **/
		if( $this->sorting ) {
			$sortColumn = Gecko_URL::getRequestParam($this->sortparam);
			$sortOrder = Gecko_URL::getRequestParam($this->sortorderparam);
			$sortcol = empty( $sortColumn ) ? $this->sortcol : $sortColumn;
			$sortord = empty( $sortOrder ) ? $this->sortorder : $sortOrder;
			$this->model->setOrder( $sortcol, $sortord );
		}
		if( $this->paginate && ($this->model instanceof Gecko_DataSource_Paginate_Interface ) ) {
			$page = (int) $this->getCurrentPage();
			if ($page > 0) {
				--$page;
			}
			$lstart = $page * $this->maxrows;
			if ($lstart < 0) {
				$lstart = 0;
			}
			$max = $this->maxrows;
			$this->model->limitResults( $lstart, $max );
		}

		/**
		 * Setup Model
		 **/
		$this->model->setup();

		/**
		 * Begin Table Construction
		 **/
		$numColumns = $this->model->getTotalColumns();
        $totalRowset = $this->model->getTotalRowset();

        $tblSettings = array('id' => $this->name, 'cellspacing' => '0', 'class' => $this->theme);
        $this->formatter->beginTable($tblSettings);

		/**
		 * If we are going to show headers...
		 **/
		if ($this->sHeaders == true) {
        	$this->formatter->beginRow('header');
			for($i = 0; $i < $numColumns; $i++) {
            	$fieldname = $this->model->getColumnAt($i);

            	if( in_array( $fieldname, $this->skipColumns ) ) {
            		continue;
            	}

            	$headerName =  $this->formatter->getColumnName($fieldname);
            	$header = $headerName;
				if ($this->sorting) {
					$sparam = array($this->sortparam => $fieldname);
					$oparam = array();
					if ($fieldname == $sortcol) {
						$img = '';
						$imgtag = '<img src="%s" border="0" alt="Sort %s %s" />';
						switch ($sortord) {
							case 'ASC':
								$img = sprintf($imgtag, $this->supi, $fieldname, "DESC");
								$oparam = array($this->sortorderparam => 'DESC');
								break;
							case 'DESC':
								$img = sprintf($imgtag, $this->sdni, $fieldname, "ASC");
								$oparam = array($this->sortorderparam => 'ASC');
                                break;
                        }
                        $uri = Gecko_URL::getSelfURI(array_merge($sparam, $oparam));
                        $header = Gecko_HTML::LinkTag( $headerName . $img, $uri );
                    } else {
                        $uri = Gecko_URL::getSelfURI($sparam);
                        $header = Gecko_HTML::LinkTag( $headerName, $uri );
                    }

                    if( in_array( $fieldname, $this->noSortColumns ) ) {
                    	$header = $headerName;
                    }
                }
                $this->formatter->addHeader($header);
			}
			$this->formatter->endRow();
        }


        /**
		 * If no rows are found...
		 **/
		if ($totalRowset == 0) {
			$noRecords = $this->_getTranslatedLabel($this->noRecords);
			$this->formatter->beginRow(0);
			$this->formatter->addCell("<div style=\"text-align: center\">$noRecords</div>", 'norecords', array("colspan" => $numColumns));
            $this->formatter->endTable();
            $this->parsed = true;
			if ($this->paginate) {
		        $this->_generatePaginator();
			}
            return true;
        }

        /**
         * All clear begin grid construction
         **/

        $isColor = false;
        if ($this->customcolors) {
            if (strpos($this->color1, "#") !== false) {
                $isColor = true;
            }
            if (strpos($this->color2, "#") !== false) {
                $isColor = true;
            }
        }

        $color = "";
        for( $i = 0; $i < $totalRowset; $i++ ) {
        	/**
        	 * Get the row from the model
        	 **/
        	$row = $this->model->getRowAt($i);
            if ($this->customcolors) {
                $color = (($i % 2) == 0 ? $this->color1 : $this->color2);
            }

            /* Set OnClick Handler */
            $js = '';
            if (!empty($this->onClick)) {
                $params = "";
                if (is_array($this->onClickParams)) {
                    $params = array();
                    foreach($this->onClickParams as $field) {
                        $params[] = "'" . $row[$field] . "'";
                    }
                    $params = implode(",", $params);
                } else {
                    if ($this->onClickParams == "NumRows") $params = $n_rows;
                    else $params = "'" . $row[$this->onClickParams] . "'";
                }
                $js = "{$this->onClick}($params)";
            }

            /* Set OnDblClick Handler */
            $js2 = '';
            if (!empty($this->ondblClick)) {
                $params = "";
                if (is_array($this->ondblClickParams)) {
                    $params = array();
                    foreach($this->onClickParams as $field) {
                        $params[] = "'" . $row[$field] . "'";
                    }
                    $params = implode(",", $params);
                } else {
                    if ($this->ondblClickParams == "NumRows") $params = $n_rows;
                    else $params = $row[$this->ondblClickParams];
                }
                $js2 = "{$this->ondblClick}($params)";
            }

            $TRSettings = array();
            if (!empty($js))
                $TRSettings['onclick'] = $js;
            if (!empty($js2))
                $TRSettings['ondblclick'] = $js2;
            if (!empty($color)) {
                if ($isColor) {
                    $TRSettings["style"] = "background: $color;";
                } else {
                    $TRSettings["class"] = $color;
                }
            }

            $this->formatter->beginRow($i, $TRSettings);

			/** Begin adding the cells to the Grid **/
			$cellNum = 0;
            foreach($row as $cell) {
            	$fieldname = $this->model->getColumnAt($cellNum);
            	$cellNum++;
            	if(in_array($fieldname, $this->skipColumns)) {
            		continue;
            	}

                $this->formatter->addCell($cell, $fieldname);
            }

            $this->formatter->endRow();
        }

        $this->formatter->endTable();

        $this->parsed = true;

		if ($this->paginate) {
        	$this->_generatePaginator();
        }
        return true;
    }

    /**
     * This function will generate the paginator
     *
     * @return void
     */
    private function _generatePaginator() {
        if (!$this->paginate) {
			throw new Gecko_DataGrid_Exception("Pagination is not enabled, use setMaxRows to enable pagination");
        }

        if (!($this->model instanceof Gecko_DataSource_Paginate_Interface)) {
			throw new Gecko_DataGrid_Exception("Grid model source must implement Gecko_DataSource_Paginate_Interface");
        }
		
        $total = (int) $this->model->getTotalRows();
        $maxPerPage = $this->maxrows;
        $currentPage = $this->getCurrentPage();

        $paginator = Zend_Paginator::factory($total);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($maxPerPage);
        $paginator->setPageRange(10);
        $this->paginator = $paginator;
        $totalPages = $paginator->count();

        if (!empty($this->navmsg)) {
            $currentPage++;
            if ($totalPages == 0) {
                $totalPages = 1;
            }
            $this->navmsg = sprintf($this->navmsg, $currentPage, $totalPages);
        }
    }
    
	/**
	 * Returns the Translated Label
	 * 
	 * @param $label
	 * @return string
	 */
	private function _getTranslatedLabel($label) {
		if (null !== ($translator = $this->getTranslator())) {
            if ($translator->isTranslated($label)) {
                $label = $translator->translate($label);
            }
        }
        
        return $label;
	}
    
	/**
     * Set translation object
     *
     * @param  Zend_Translate|Zend_Translate_Adapter|null $translator
     * @return Zend_Validate_Abstract
     */
    public function setTranslator($translator = null) {
        if ((null === $translator) || ($translator instanceof Zend_Translate_Adapter)) {
            $this->_translator = $translator;
        } elseif ($translator instanceof Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        } else {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Invalid translator specified');
        }
        return $this;
    }

    /**
     * Return translation object
     *
     * @return Zend_Translate_Adapter|null
     */
    public function getTranslator() {
        if ($this->translatorIsDisabled()) {
            return null;
        }

        if (null === $this->_translator) {
            return self::getDefaultTranslator();
        }

        return $this->_translator;
    }
	
	/**
     * Set default translation object for all validate objects
     *
     * @param  Zend_Translate|Zend_Translate_Adapter|null $translator
     * @return void
     */
    public static function setDefaultTranslator($translator = null) {
        if ((null === $translator) || ($translator instanceof Zend_Translate_Adapter)) {
            self::$_defaultTranslator = $translator;
        } elseif ($translator instanceof Zend_Translate) {
            self::$_defaultTranslator = $translator->getAdapter();
        } else {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Invalid translator specified');
        }
    }

    /**
     * Get default translation object for all validate objects
     *
     * @return Zend_Translate_Adapter|null
     */
    public static function getDefaultTranslator() {
        if (null === self::$_defaultTranslator) {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $translator = Zend_Registry::get('Zend_Translate');
                if ($translator instanceof Zend_Translate_Adapter) {
                    return $translator;
                } elseif ($translator instanceof Zend_Translate) {
                    return $translator->getAdapter();
                }
            }
        }

        return self::$_defaultTranslator;
    }
    
	/**
     * Indicate whether or not translation should be disabled
     *
     * @param  bool $flag
     * @return Zend_Validate_Abstract
     */
    public function setDisableTranslator($flag) {
        $this->_translatorDisabled = (bool) $flag;
        return $this;
    }

    /**
     * Is translation disabled?
     *
     * @return bool
     */
    public function translatorIsDisabled() {
        return $this->_translatorDisabled;
    }
}