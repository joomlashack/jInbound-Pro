<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
 @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.controlleradmin');
JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('url');

class JInboundControllerReports extends JControllerAdmin
{
	public function getModel($name='Reports', $prefix = 'JInboundModel') {
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
	
	public function exportleads() {
		$this->export('leads');
	}
	
	public function exportpages() {
		$this->export('pages');
	}
	
	protected function export($layout) {
		$this->setRedirect(JInboundHelperUrl::view('reports', false, array('format' => 'csv', 'layout' => $layout)));
	}
}