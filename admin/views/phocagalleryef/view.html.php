<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.view');


class PhocaGalleryCpViewPhocaGalleryEf extends JViewLegacy
{
	protected $item;
	protected $form;
	protected $state;
	protected $t;
	protected $r;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{


		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->t['ftp']		= JClientHelper::setCredentialsFromRequest('ftp');
		$this->t		= new StdClass;
		$model 			= $this->getModel();

		$this->t	= PhocaGalleryUtils::setVars('ef');
		$this->r	= new PhocaGalleryRenderAdminview();

		// Set CSS for codemirror
		JFactory::getApplication()->setUserState('editor.source.syntax', 'css');


		// New or edit
		if (!$this->form->getValue('id') || $this->form->getValue('id') == 0) {
			$this->form->setValue('source', null, '');
			$this->form->setValue('type', null, 2);
			$this->t['suffixtype'] = JText::_('COM_PHOCAGALERY_WILL_BE_CREATED_FROM_TITLE');

		} else {
			$this->source	= $model->getSource($this->form->getValue('id'), $this->form->getValue('filename'), $this->form->getValue('type'));
			$this->form->setValue('source', null, $this->source->source);
			$this->t['suffixtype'] = '';
		}

		// Only help input form field - to display Main instead of 1 and Custom instead of 2
		if ($this->form->getValue('type') == 1) {
			$this->form->setValue('typeoutput', null, JText::_('COM_PHOCAGALLERY_MAIN_CSS'));
		} else {
			$this->form->setValue('typeoutput', null, JText::_('COM_PHOCAGALLERY_CUSTOM_CSS'));
		}



		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagalleryefs.php';
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$bar 		= JToolbar::getInstance('toolbar');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaGalleryEfsHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		$paramsC 	= JComponentHelper::getParams('com_phocagallery');

		$text = $isNew ? JText::_( 'COM_PHOCAGALLERY_NEW' ) : JText::_('COM_PHOCAGALLERY_EDIT');
		JToolbarHelper ::title(   JText::_( 'COM_PHOCAGALLERY_STYLE' ).': <small><small>[ ' . $text.' ]</small></small>' , 'eye');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			JToolbarHelper ::apply('phocagalleryef.apply', 'JToolbar_APPLY');
			JToolbarHelper ::save('phocagalleryef.save', 'JToolbar_SAVE');
		}

		JToolbarHelper ::cancel('phocagalleryef.cancel', 'JToolbar_CLOSE');
		JToolbarHelper ::divider();
		JToolbarHelper ::help( 'screen.phocagallery', true );
	}

}
?>
