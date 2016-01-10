<?php
class Gecko_Controller_Form_Standard extends Gecko_Controller_Form
{
	protected $model;
	protected $title;
	protected $id_param;
	protected $editMode = false;
	protected $editTitle = '';
	protected $createTitle = '';
	
	protected function isEditMode()
	{
		return $this->editMode;
	}

	protected function setIdParam($id_param)
	{
		$this->id_param = $id_param;

		return $this;
	}

	protected function setTitles($createTitle, $editTitle)
	{
		$this->createTitle = $createTitle;
		$this->editTitle = $editTitle;

		return $this;
	}

	protected function setModel(Gecko_Model_Base $model)
	{
		$this->model = $model;

		return $this;
	}

	public function getModel()
	{
		return $this->model;
	}

	protected function checkRequiredParams()
	{
		if (!($this->model) || !($this->model instanceof Gecko_Model_Base)) {
			throw new DomainException('Invalid Model');
		}

		$form = $this->getForm();
		if (!($form) || !($form instanceof Zend_Form)) {
			throw new DomainException('Invalid Form');
		}

		if (empty($this->id_param)) {
			throw new DomainException('Empty Param Id');
		}
	}
	
	public function onBind()
	{
		$Request = $this->getRequest();
		$id = (int) $Request->getParam($this->id_param, 0);
		if ($id > 0) {
			$Entity = $this->model->findByPk($id);
			$this->title = $this->editTitle;
			$this->editMode = true;
		} else {
			$Entity = $this->model->createNew();
			$this->title = $this->createTitle;
		}
	
		return $Entity;
	}
	
	public function onFormInvalid()
	{
		$this->editMode = true;
	}
	
	public function indexAction()
	{
		$this->checkRequiredParams();

		// Process Form
		parent::indexAction();
	
		$this->view->formTitle = $this->title;
		$this->view->editMode = $this->editMode;
	}	
}