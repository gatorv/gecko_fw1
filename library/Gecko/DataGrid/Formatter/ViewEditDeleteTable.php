<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once( 'Gecko/DataGrid/Formatter/Table.php' );

/**
 * Advanced formatter that adds a View/Edit/Delete link
 * at the end of the table
 *
 * @package Gecko.DataGrid.Formatter;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataGrid_Formatter_ViewEditDeleteTable extends Gecko_DataGrid_Formatter_Table {
	const HEADER_TITLE = 'Links';
	const LABEL_EDIT = 'Edit';
	const LABEL_DELETE = 'Delete';
	const LABEL_VIEW = 'View';
	
	/**
	 * The Column that contains the key
	 * to add to the link
	 *
	 * @var string
	 */
	private $_idColumn;
	
	/**
	 * The Settings array
	 *
	 * @var array
	 */
	private $_settings = array();
	
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
	 * Creates a new instance of the formatter,
	 * it needs a settings array and the name
	 * of the id column
	 *
	 * @param array The settings
	 * @param string The column name
	 */
	public function __construct($idColumn, $aSettings = array()) {
		$this->_idColumn = $idColumn;
		$this->_settings = $this->_checkSettings($aSettings);
	}
	
	/**
	 * Disables the view Link
	 * 
	 * @return $this
	 */
	public function disableViewLink() {
		$this->_settings['viewLink'] = false;
		
		return $this;
	}
	
	/**
	 * Disables the Edit Link
	 * 
	 * @return $this
	 */
	public function disableEditLink() {
		$this->_settings['editLink'] = false;
		
		return $this;
	}
	
	/**
	 * Disables the delete link
	 * 
	 * @return $this
	 */
	public function disableDeleteLink() {
		$this->_settings['deleteLink'] = false;
		
		return $this;
	}
	
	/**
	 * Sets the View Label
	 * 
	 * @param string $sLabel
	 * @return $this
	 */
	public function setViewLabel($sLabel) {
		$this->_settings['viewLabel'] = $sLabel;
		
		return $this;
	}
	
	/**
	 * Sets the Edit Label
	 * 
	 * @param string $sLabel
	 * @return $this
	 */
	public function setEditLabel($sLabel) {
		$this->_settings['editLabel'] = $sLabel;
		
		return $this;
	}
	
	/**
	 * Sets the Delete Label
	 * 
	 * @param string $sLabel
	 * @return $this
	 */
	public function setDeleteLabel($sLabel) {
		$this->_settings['deleteLabel'] = $sLabel;
		
		return $this;
	}
	
	/**
	 * Creates a route in the link
	 * 
	 * @return string
	 */
	private function _createRoute($action, $sValue) {
		$front = Zend_Controller_Front::getInstance();
		$request = $front->getRequest();
		
		$path = array();

		if ($request->getModuleName() != 'default' && $request->getModuleName() != '') { 
			$path['module'] = $request->getModuleName();
		}

		$path['controller'] = $request->getControllerName();
		$path['action'] = $action;
		$path[$this->_idColumn] = $sValue;
		
		return $front->getRouter()->assemble($path);
	}
	
	/**
	 * Checks and populates default settings, by default all of
	 * the columns are visible
	 * 
	 * @param array $settings
	 * @return array
	 */
	private function _checkSettings(array $settings) {
		if (!isset($settings['viewLink'])) {
			$settings['viewLink'] = true;
			$settings['viewLabel'] = $this->_getTranslatedLabel(self::LABEL_VIEW);
			$settings['viewImage'] = '/images/view.png';
		}
		
		if (!isset($settings['editLink'])) {
			$settings['editLink'] = true;
			$settings['editLabel'] = $this->_getTranslatedLabel(self::LABEL_EDIT);
			$settings['editImage'] = '/images/update.png';
		}
		
		if (!isset($settings['deleteLink'])) {
			$settings['deleteLink'] = true;
			$settings['deleteLabel'] = $this->_getTranslatedLabel(self::LABEL_DELETE);
			$settings['deleteImage'] = '/images/delete.png';
		}
		
		$settings['header'] = $this->_getTranslatedLabel(self::HEADER_TITLE);
		
		return $settings;
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
	 * Returns if a row is valid to add the links
	 */
	protected function isValidRow() {
		return true;
	}

	/**
	 * Called when row ends
	 */
	public function endRow() {
		if( $this->rowNum === 'header' ) {
			$span = 0;
			if( $this->_settings['viewLink'] == true ) $span++;
			if( $this->_settings['editLink'] == true ) $span++;
			if( $this->_settings['deleteLink'] == true ) $span++;

			if( $span > 0 ) {
				$this->table->addHeader( $this->_settings['header'], array( "colspan" => $span ) );
			}
		} else {
			if( !$this->isValidRow() ) {
				return;
			}
			
			$data = $this->grid->getDataSource()->getRowAt($this->rowNum);
			$key = $data[$this->_idColumn];
			if(!$key) {
				throw new Gecko_DataGrid_Exception("{$this->_idColumn} not found in data source");
			}

			if( $this->_settings['viewLink'] == true ) {
				$viewLabel = $this->_settings['viewLabel'];
				$view = Gecko_HTML::LinkTag( 
					Gecko_HTML::drawImg($this->_settings['viewImage'], $viewLabel) . ' ' . $viewLabel, 
					$this->_createRoute('view', $key));
				$this->table->addCell( $view );
			}
			
			if( $this->_settings['editLink'] == true ) {
				$editLabel = $this->_settings['editLabel'];
				$edit = Gecko_HTML::LinkTag(
					Gecko_HTML::drawImg($this->_settings['editImage'], $editLabel) . ' ' . $editLabel,
					$this->_createRoute('edit', $key));
				$this->table->addCell( $edit );
			}
			
			if( $this->_settings['deleteLink'] == true ) {
				$deleteLabel = $this->_settings['deleteLabel'];
				$delete = Gecko_HTML::LinkTag(
					Gecko_HTML::drawImg($this->_settings['deleteImage'], $deleteLabel) . ' ' . $deleteLabel,
					$this->_createRoute('delete', $key), 
					'_self', 
					array('class' => 'deleteLink'));
				$this->table->addCell( $delete );
			}
		}
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