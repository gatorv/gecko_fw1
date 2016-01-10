<?php
abstract class Gecko_Controller_Form extends Gecko_Controller
{
	private $form;
	/**
	 * The entity to use the form
	 * @var Zend_Db_Table_Row_Abstract
	 */
	private $entity;
	
	/**
	 * Set the form that it's used by the controller
	 * @param Zend_Form $Form
	 */
	public function setForm(Zend_Form $Form)
	{
		$this->form = $Form;
		return $this;
	}
	
	public function getForm()
	{
		return $this->form;
	}
	
	public function getEntity()
	{
		return $this->entity;
	}
	
	/**
	 * Main entry point for this form controller
	 * you must implement the form.phtml for it to work
	 * correctly
	 */
	public function indexAction()
	{
		$this->entity = $this->onBind();
		if (!($this->entity instanceof Zend_Db_Table_Row_Abstract)) {
			throw new Gecko_Exception('A entity instance of Zend_Db_Table_Row_Abstract is required');
		}
		$Form = $this->getForm();
		$Form->populate($this->entity->toArray());
		$this->afterPopulate();
		$Request = $this->getRequest();
		
		if ($Request->isPost()) {
			if ($this->getForm()->isValid($Request->getPost())) {
				$this->onSubmit();
				$this->onSuccess();
			} else {
				$this->onFormInvalid();
			}
		}
		
		$this->view->form = $this->getForm();
	}
	
	/**
	 * Called after the values has been populated
	 */
	public function afterPopulate()
	{
		
	}
	
	/**
	 * Called upon succesfull insert
	 */
	public function onSuccess()
	{
		
	}
	
	/**
	 * Called when form is invalid
	 */
	public function onFormInvalid()
	{
		
	}
	
	/**
	 * Called before the request is processed, it must return
	 * a abstract Zend_Db_Table_Row_Abstract entity to use in the form
	 * 
	 * @return Zend_Db_Table_Row_Abstract
	 */
	abstract public function onBind();
	
	/**
	 * Must be implemented by base controllers
	 * called when the form is submitted and valid
	 * it returns the binded (and saved) entity
	 * 
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function onSubmit()
	{
		$values = $this->getFormValues();
		$Entity = $this->getEntity();
		$Entity->setFromArray($values);
		
		// Dispatch before save
		$this->beforeSave();
		// Save the entity
		$Entity->save();
		// Dispatch after save
		$this->afterSave();
		
		return $Entity;
	}
	
	/**
	 * Called before saving the entity
	 * @return void
	 */
	public function beforeSave()
	{
		
	}
	
	/**
	 * Called after saving the entity
	 * @return void
	 */
	public function afterSave()
	{
		
	}
	
	/**
	 * Returns the form values, useful to preprocess the form values
	 * or add or delete things
	 * 
	 * @return array
	 */
	protected function getFormValues()
	{
		return $this->getForm()->getValues();
	}
}