<?php
class Gecko_Date extends DateTime
{
	const MYSQL_DATE_FORMAT = 'Y-m-d';
	
	public static function checkDate($date, $format = '')
	{
		if (empty($format)) {
			if (Zend_Registry::isRegistered('date_format')) {
				$format = Zend_Registry::get('date_format');
			} else {
				$format = self::MYSQL_DATE_FORMAT;
			}
		}
		
		return Zend_Locale_Format::checkDateFormat($date, array('date_format' => $format));
	}
	
	public function formatDate($format = '')
	{
		if (empty($format)) {
			if (Zend_Registry::isRegistered('date_format')) {
				$format = Zend_Registry::get('date_format');
			} else {
				$format = 'd-m-Y';	
			}
		}
		
		$parsed = $this->format($format);
		
		if (Zend_Registry::isRegistered('Zend_Locale')) {
			$locale = Zend_Registry::get('Zend_Locale');
			
			if ($locale->getLanguage() == 'es' && preg_match('/l/', $format)) {
				$parsed = preg_replace_callback('/([a-z]){1}/', function($matches) {
					return strtoupper($matches[0]);
				}, $parsed); // Fix for lowercase months in es locale (matches only characters)
			}
		}
	
		return $parsed;
	}
	
	public function formatTime($format = '')
	{
		if (empty($format)) {
			if (Zend_Registry::isRegistered('time_format')) {
				$format = Zend_Registry::get('time_format');
			} else {
				$format = 'h:i A';
			}
		}
		
		$time = $this->format($format);
	
		return $time;
	}
	
	public function formatDateTime()
	{
		return $this->formatDate() . ' ' . $this->formatTime();
	}
	
	public function toMySQLDate()
	{
		return $this->format(self::MYSQL_DATE_FORMAT);
	}
	
	public function toMySQLDateTime()
	{
		$format = sprintf('%s %s', self::MYSQL_DATE_FORMAT, 'H:i:s');
		
		return $this->format($format);
	}
	
	public function toShortDate()
	{
		$format = "l d \d\e Y";
		
		$parsed = $this->format($format);
		
		return ucfirst($parsed);
	}
	
	public function __toString()
	{
		return $this->formatDateTime();
	}
}