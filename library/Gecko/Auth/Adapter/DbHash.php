<?php
class Gecko_Auth_Adapter_DbHash extends Zend_Auth_Adapter_DbTable
{
	/**
	 * _authenticateCreateSelect() - This method creates a Zend_Db_Select object that
	 * is completely configured to be queried against the database.
	 *
	 * @return Zend_Db_Select
	 */
	protected function _authenticateCreateSelect()
    {
        $credentialExpression = new Zend_Db_Expr(
            'COUNT(*) AS '
            . $this->_zendDb->quoteIdentifier(
                $this->_zendDb->foldCase('zend_auth_credential_match')
              )
        );

        // get select
        $dbSelect = clone $this->getDbSelect();
        $dbSelect->from($this->_tableName, array('*', $credentialExpression))
                 ->where($this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ?', $this->_identity);

        return $dbSelect;
    }
    
    /**
     * _authenticateValidateResultSet() - This method attempts to make
     * certain that only one record was returned in the resultset
     *
     * @param array $resultIdentities
     * @return true|Zend_Auth_Result
     */
    protected function _authenticateValidateResultSet(array $resultIdentities)
    {
    	if (count($resultIdentities) == 1) {
    		return true;
    	} else {
    		$this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
    		$this->_authenticateResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
    		return $this->_authenticateCreateAuthResult();
    	}
    }
    
    /**
     * _authenticateValidateResult() - This method attempts to validate that
     * the record in the resultset is indeed a record that matched the
     * identity provided to this adapter.
     *
     * @param array $resultIdentity
     * @return Zend_Auth_Result
     */
    protected function _authenticateValidateResult($resultIdentity)
    {
    	$zendAuthCredentialMatchColumn = $this->_zendDb->foldCase('zend_auth_credential_match');
    
    	if ($resultIdentity[$zendAuthCredentialMatchColumn] != '1') {
    		$this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    		$this->_authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
    		return $this->_authenticateCreateAuthResult();
    	}
    	
    	unset($resultIdentity[$zendAuthCredentialMatchColumn]);
    	$this->_resultRow = $resultIdentity;
    	
    	// Validate password
    	$hash = $resultIdentity[$this->_credentialColumn];
    	if (!function_exists('password_verify')) {
    		require_once 'Gecko/password.php'; // Compatibility layer
    	}
    	if (!password_verify($this->_credential, $hash)) {
    		$this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    		$this->_authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
    		return $this->_authenticateCreateAuthResult();
    	}
    
    	$this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
    	$this->_authenticateResultInfo['messages'][] = 'Authentication successful.';
    	return $this->_authenticateCreateAuthResult();
    }
}