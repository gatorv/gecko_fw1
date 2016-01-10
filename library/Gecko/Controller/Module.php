<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Controller/Action.php';

/**
 * Gecko_Controller_Module
 *
 * @package Gecko.Controller;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Controller_Module extends Zend_Controller_Action {
	const PRIMARY_KEY_NOTFOUND = 'primaryKeyNotFound';
	const PRIMARY_KEY_MISSING = 'primaryKeyMissing';
	const RECORD_SAVED = 'recordSavedOk';
	const RECORD_DELETED = 'recordDeletedOk';
	const DEFAULT_LIST = 'list';
	const DEFAULT_FORM = 'form';
	
	/**
	 * Module Messages
	 * @var array
	 */
	protected $_messsageMap = array(
		self::PRIMARY_KEY_MISSING => 'The primary Key is missing in the request',
		self::PRIMARY_KEY_NOTFOUND => 'The primary Key hasn\'t been found in the dataset',
		self::RECORD_SAVED => 'Record %s saved sucessfully',
		self::RECORD_DELETED => 'Record %s deleted successfully',
	);
	
	/**
	 * The Form Class to use (Zend_Form)
	 * @var string
	 */
	protected $_formClass;
	
	/**
	 * The Grid Class to use (Gecko_DataGrid)
	 * @var string
	 */
	protected $_gridClass = 'Gecko_DataGrid';
	
	/**
	 * The Model Class to use
	 * 
	 * @var string
	 */
	protected $_modelClass;
	
	protected $_listScript = 'list';
	protected $_formScript = 'form';
	protected $_viewScript = 'view';
	protected $_gridColumns;
	protected $_gridNames;
	
	protected $_form;
	protected $_grid;
	protected $_model;
	protected $_name;
	protected $_moduleName;
	protected $_redirector;
	protected $_primary;
	
	/**
	 * The Formatter of the Grid
	 * 
	 * @var mixed
	 */
	protected $_gridFormatter;
	
	/**
	 * The Grid DataSource
	 * 
	 * @var mixed
	 */
	protected $_gridModelSource;
	
	/**
	 * The Grid Query to use if needed
	 * 
	 * @var string
	 */
	protected $_gridQuery;
	
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
	 * Initializes Module Settings
	 * @return void
	 */
	public function init() {
		// Execute Setup
		$this->_setup();
		
		// Check Required Stuff
		if (empty($this->_formClass)) {
			throw new Gecko_Exception('$_formClass not initialized');
		}
		if (empty($this->_modelClass)) {
			throw new Gecko_Exception('$_modelClass not initialized');
		}
		if (empty($this->_gridClass)) {
			throw new Gecko_Exception('$_gridClass not initialized');
		}
		if (empty($this->_name)) {
			$this->_name = get_class($this);
		}
		if (empty($this->_moduleName)) {
			$this->_moduleName = $this->_name;
		}
		
		// -- Setup Model
		$this->_setupModel();
		
		// -- Ensure Model Compatibility
		if (!($this->_model instanceof Zend_Db_Table_Abstract)) {
			throw new Gecko_Exception('The used model must be compatible with Zend_Db_Table_Abstract');
		}
		
		// -- Setup Primary Primary
		$this->_setupPrimaryKey();
		
		// Setup basic View Stuff
		if ($this->_listScript == self::DEFAULT_LIST && $this->_formScript == self::DEFAULT_FORM) {
			$this->_helper->viewRenderer->setNoController(true);
		}
		// Redirector
		$this->_redirector = $this->_helper->getHelper('Redirector');
	}
	
	/**
	 * Execute custom Setup for module
	 * 
	 * @return void
	 */
	protected function _setup() {}
	
	/**
	 * Initiates the Grid
	 * 
	 * @return void
	 */
	protected function _setupGrid() {
		// Setup Table Formatter
		if ($this->_gridFormatter == null) {
			$this->_gridFormatter = new Gecko_DataGrid_Formatter_ViewEditDeleteTable($this->_primary);
		}
		
		if (is_array($this->_gridNames)) {
        	$this->_gridFormatter->setColumnNames($this->_gridNames);
		}
        
        // Construct Grid 
		$this->_grid = new $this->_gridClass(
			$this->_name, 
			$this->_gridModelSource, 
			array(
				'sorting' => array(
					'sortColumn' => $this->_primary, 
					'sortOrder' => 'ASC'
				),
				'paginate' => 20
			),
			$this->_gridFormatter
		);
	}
	
	/**
	 * Binds the Data Source
	 * 
	 * @return void
	 */
	protected function _setupDataSource() {
		// Bind DataSource
		if ($this->_gridModelSource == null) {
			$this->_gridModelSource = new Gecko_DataSource_Table_DBTable($this->_model);
			// If we have a list of columns to fetch, set them
			if (is_array($this->_gridColumns)) {
				$this->_gridModelSource->setColumns($this->_gridColumns);
			}
		}
	}
	
	/**
	 * Setups the Model
	 * 
	 * @return void
	 */
	protected function _setupModel() {
		$this->_model = Gecko_Model::factory($this->_modelClass);
	}
	
	/**
	 * Setups the Primary Key
	 * 
	 * @return void
	 */
	protected function _setupPrimaryKey() {
		// Get Primary Key
		if ($this->_primary == null) {
			$aInfo = $this->_model->info();
			$sPrimary = $aInfo['primary'][1];
			$this->_primary = $sPrimary;
		}
	}
	
	/**
	 * Saves a new Registry
	 * 
	 * @param $values
	 * @return int Primary Key
	 */
	protected function _add($aValues) {
		$row = $this->_model->createRow();
		$aCols = $this->_model->info(Zend_Db_Table_Abstract::COLS);
		
		foreach($aCols as $sField) {
			if ($sField == $this->_primary) {
				continue;
			}
			
			$row[$sField] = $aValues[$sField];
		}
		
		$row->save();
		
		return $row->{$this->_primary};
	}
	
	/**
	 * Edits the current values from form
	 * 
	 * @param array $aValues
	 * @param string $pKey
	 * @return void
	 */
	protected function _edit($aValues, $pKey) {
		$row = $this->_model->find($pKey)->current();
		if (!($row instanceof Zend_Db_Table_Row_Abstract)) {
			throw new Exception("'$pKey' Key not found");
		}
		
		$aCols = $this->_model->info(Zend_Db_Table_Abstract::COLS);
		
		foreach($aCols as $sField) {
			if ($sField == $this->_primary) {
				continue;
			}
			if (isset($aValues[$sField])) {
				$row[$sField] = $aValues[$sField];
			}
		}
		
		$row->save();
	}
	
	/**
	 * Deletes a Record
	 * 
	 * @param int $pKey
	 * @return void
	 */
	protected function _delete($pKey) {
		$row = $this->_model->find($pKey)->current();
		if (!($row instanceof Zend_Db_Table_Row_Abstract)) {
			throw new Exception("'$pKey' Key not found");
		}
		
		$row->delete();
	}
	
	/**
	 * By Default there is no 'index'
	 * 
	 * @return void
	 */
	public function indexAction() {
		$this->_redirector->gotoSimple('list');
	}
	
	/**
	 * Lists all records in a Grid
	 * 
	 * @return void
	 */
	public function listAction() {
		// Setup DataSource
		$this->_setupDataSource();
		// Setup Grid
		$this->_setupGrid();
		// Build Grid
		$this->_grid->buildTable();
		
		// Setup View Script
		$this->_helper->viewRenderer($this->_listScript);
		$this->view->grid = $this->_grid;
		$this->view->addLink = array('action' => 'add');
		$this->view->moduleName = $this->_moduleName;
	}
	
	/**
	 * Draws add Form and adds record
	 * 
	 * @return void
	 */
	public function addAction() {
		$this->_form = new $this->_formClass();
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			if ($this->_form->isValid($request->getPost())) {
				$values = $this->_form->getValues();
				try {
					$sKey = $this->_add($values);
					$this->_helper->FlashMessenger(
						sprintf($this->_getTranslatedLabel(self::RECORD_SAVED),
						$sKey
					));
					$this->_redirector->gotoSimple('list');
				} catch (Exception $e) {
					$error = $e->getMessage();
					$this->view->error = $error;
				}
			}
		}
		
		$this->view->form = $this->_form;
		$this->view->moduleName = $this->_moduleName;
		$this->_helper->viewRenderer($this->_formScript);
	}
	
	/**
	 * Draws the edit form for editing
	 * 
	 * @return void
	 */
	public function editAction() {
		$this->_form = new $this->_formClass();
		$request = $this->getRequest();
		
		$pKey = $request->getParam($this->_primary);
		if ($pKey == '') {
			$this->_helper->FlashMessenger($this->_getTranslatedLabel(self::PRIMARY_KEY_MISSING));
			$this->_redirector->gotoSimple('list');
		}
		
		$row = $this->_model->find($pKey)->current();
		$this->_form->populate($row->toArray());
		$this->_postPopulate($pKey);
		
		if ($request->isPost()) {
			if ($this->_form->isValid($request->getPost())) {
				$values = $this->_form->getValues();
				try {
					$this->_edit($values, $pKey);
					$this->_helper->FlashMessenger(
						sprintf($this->_getTranslatedLabel(self::RECORD_SAVED),
						$pKey
					));
					
					$this->_redirector->gotoSimple('list');
				} catch (Exception $e) {
					$error = $e->getMessage();
					$this->view->error = $error;
				}
			}
		}
		
		$this->view->form = $this->_form;
		$this->view->moduleName = $this->_moduleName;
		$this->_helper->viewRenderer($this->_formScript);
	}
	
	/**
	 * Called after Form Population
	 * 
	 * @return void
	 */
	protected function _postPopulate() {}
	
	/**
	 * Displays a Record without links for editing
	 * 
	 * @return void
	 */
	public function viewAction() {
		$form = clone $this->_form;
		$form->removeElement('submit');
		$request = $this->getRequest();
		
		$pKey = $request->getParam($this->_primary);
		if ($pKey == '') {
			$this->_helper->FlashMessenger($this->_getTranslatedLabel(self::PRIMARY_KEY_MISSING));
			$this->_redirector->gotoSimple('list');
		}
		
		$row = $this->_model->find($pKey)->current();
		$form->populate($row->toArray());
		
		$this->view->form = $form;
		$this->view->moduleName = $this->_moduleName;
		$this->_helper->viewRenderer($this->_formScript);
	}
	
	/**
	 * Performs a delete action
	 * 
	 * @return void
	 */
	public function deleteAction() {
		$request = $this->getRequest();
		$pKey = $request->getParam($this->_primary);
		if ($pKey == '') {
			$this->_helper->FlashMessenger($this->_getTranslatedLabel(self::PRIMARY_KEY_MISSING));
			$this->_redirector->gotoSimple('list');
		}
		
		try {
			$this->_delete($pKey);
			$this->_helper->FlashMessenger(
				sprintf($this->_getTranslatedLabel(self::RECORD_DELETED),
				$pKey
			));
		} catch (Exception $e) {
			$error = $e->getMessage();
			$this->_helper->FlashMessenger($error);
		}
		
		$this->_redirector->gotoSimple('list');
	}
	
	/**
	 * Returns the Translated Label
	 * 
	 * @param $label
	 * @return string
	 */
	private function _getTranslatedLabel($label) {
		$message = $this->_messsageMap[$label];
		if (null !== ($translator = $this->getTranslator())) {
            if ($translator->isTranslated($message)) {
                $message = $translator->translate($message);
            }
        }
        
        return $message;
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