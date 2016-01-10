<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/** Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';

/** Zend_Log_Formatter_Simple */
require_once 'Zend/Log/Formatter/Simple.php';

/**
 * Log Writer that supports log rotation and max file size
 * 
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 */
class Gecko_Log_Writer_Rotate extends Zend_Log_Writer_Abstract
{
    /**
     * Holds the PHP stream to log to.
     *
     * @var null|stream
     */
    protected $_stream = null;

    /**
     * Class Constructor
     *
     * @param string $sPath Path to Logs
     * @param array $aOptions Options for rotation
     * @return void
     * @throws Zend_Log_Exception
     */
    public function __construct($sPath, $aOptions = array())
    {
        if (!is_dir($sPath)) {
        	require_once 'Zend/Log/Exception.php';
        	throw new Zend_Log_Exception('A Path is expected to save logs');
        }
        
        $aConfig = self::_parseConfig($aOptions);
        $aConfig = array_merge(array(
            'filename' => 'ZendApp_%d%m%Y.log', // Supports strftime() options
            'max_log_size' => 2000000, // In bytes
        	'mode' => 'a'
        ), $aConfig);
        
        $sFileName = strftime($aConfig['filename'], time());
        $sFile = $sPath . DIRECTORY_SEPARATOR . $sFileName;
        // Check Rotation
        if (file_exists($sFile)) {
        	// If file exists and larger than max size
        	// Copy and create new one
        	$nMaxFileSize = $aConfig['max_log_size'];
        	if (filesize($sFile) > $nMaxFileSize) {
        		$aMatches = glob($sFileName . '*');
        		if (is_array($aMatches) && count($aMatches) >= 1) {
        			$nCount = count($aMatches);
        		} else {
        			$nCount = 1;
        		}
        		$sNewName = $sPath . DIRECTORY_SEPARATOR . $sFileName . '.' . $nCount;
        		@rename($sFile, $sNewName);
        	}
        }
        
        $sMode = $aConfig['mode'];
        
    	if (!$this->_stream = @fopen($sFile, $sMode, false)) {
    		require_once 'Zend/Log/Exception.php';
			$msg = "\"$sFile\" cannot be opened with mode \"$sMode\"";
			throw new Zend_Log_Exception($msg);
		}

        $this->_formatter = new Zend_Log_Formatter_Simple();
    }

    /**
     * Create a new instance of Gecko_Log_Writer_Rotate
     *
     * @param  array|Zend_Config $config
     * @return Zend_Log_Writer_Stream
     */
    static public function factory($config)
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array(
            'path' => null,
        ), $config);

        $path = $config['path'];

        return new self(
            $path,
            $config
        );
    }

    /**
     * Close the stream resource.
     *
     * @return void
     */
    public function shutdown()
    {
        if (is_resource($this->_stream)) {
            fclose($this->_stream);
        }
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     * @throws Zend_Log_Exception
     */
    protected function _write($event)
    {
        $line = $this->_formatter->format($event);

        if (false === @fwrite($this->_stream, $line)) {
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception("Unable to write to stream");
        }
    }
}