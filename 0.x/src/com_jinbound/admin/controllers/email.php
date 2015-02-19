<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerEmail extends JInboundFormController
{
	public function test()
	{
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		JInbound::registerHelper('path');
		require_once JInboundHelperPath::admin('models/emails.php');
		// init
		$dispatcher = JDispatcher::getInstance();
		$app        = JFactory::getApplication();
		$input      = $app->input;
		$to         = $input->get('to', '', 'string');
		$fromname   = $input->get('fromname', '', 'string');
		$fromemail  = $input->get('fromemail', '', 'string');
		$subject    = $input->get('subject', '', 'string');
		$htmlbody   = $input->get('htmlbody', '', 'raw');
		$plainbody  = $input->get('plainbody', '', 'string');
		$type       = $input->get('type', '', 'string');
		// check
		foreach (array('to', 'fromname', 'fromemail', 'subject', 'type') as $var)
		{
			if (empty($$var))
			{
				throw new Exception("Variable $var cannot be empty");
			}
		}
		// setup data for email tags
		$result = new stdClass();
		switch ($type)
		{
			case 'report':
				$tags = array(
					'reports.goals.count', 'reports.goals.percent',
					'reports.leads.count', 'reports.leads.percent', 'reports.leads.list',
					'reports.pages.hits', 'reports.pages.list'
				);
				$result->goals = (object) array('count' => 201, 'percent' => 11.0);
				$result->leads = (object) array('count' => 302, 'percent' => 0.0, 'list' => '<table>'
					. '<thead><tr>'
					. '<th>' . JText::_('COM_JINBOUND_NAME') . '</th>'
					. '<th>' . JText::_('COM_JINBOUND_DATE') . '</th>'
					. '<th>' . JText::_('COM_JINBOUND_FORM_CONVERTED_ON') . '</th>'
					. '<th>' . JText::_('COM_JINBOUND_LANDING_PAGE_NAME') . '</th>'
					. '</tr></thead>'
					. '<tbody>'
					. '<tr>'
					. '<td>John Smith</td><td>2015-01-05 12:34:56</td>'
					. '<td>My Form</td><td>Example Page</td>'
					. '</tr>'
					. '<tr>'
					. '<td>Martha Jones</td><td>2015-01-05 01:23:45</td>'
					. '<td>My Form</td><td>Example Page</td>'
					. '</tr>'
					. '<tr>'
					. '<td>Rose Tyler</td><td>2015-01-04 16:27:02</td>'
					. '<td>My Form</td><td>Example Page</td>'
					. '</tr>'
					. '<tr>'
					. '<td>Amy Pond</td><td>2015-01-03 08:11:43</td>'
					. '<td>My Form</td><td>Example Page</td>'
					. '</tr>'
					. '<tr>'
					. '<td>Rory Williams</td><td>2015-01-02 11:51:16</td>'
					. '<td>My Form</td><td>Example Page</td>'
					. '</tr>'
					. '</tbody>'
					. '</table>');
				$result->pages = (object) array('hits' => 1902, 'list' => '<table>'
					. '<thead><tr>'
					. '<th>' . JText::_('COM_JINBOUND_LANDING_PAGE_NAME') . '</th>'
					. '<th>' . JText::_('COM_JINBOUND_VISITS') . '</th>'
					. '<th>' . JText::_('COM_JINBOUND_SUBMISSIONS') . '</th>'
					. '<th>' . JText::_('COM_JINBOUND_LEADS') . '</th>'
					. '<th>' . JText::_('COM_JINBOUND_GOAL_COMPLETIONS') . '</th>'
					. '<th>' . JText::_('COM_JINBOUND_GOAL_COMPLETION_RATE') . '</th>'
					. '</tr></thead>'
					. '<tbody>'
					. '<tr>'
					. '<td>Example Page</td>'
					. '<td>1902</td>'
					. '<td>341</td>'
					. '<td>302</td>'
					. '<td>201</td>'
					. '<td>11.0%</td>'
					. '</tr>'
					. '</tbody>'
					. '</table>');
				break;
			case 'campaign':
			default:
				$tags = array('email.campaign_name', 'email.form_name');
				$result->lead = new stdClass();
				$result->lead->first_name = 'Howard';
				$result->lead->last_name  = 'Moon';
				$result->lead->email      = $to;
				$result->campaign_name    = 'Test Campaign';
				$result->form_name        = 'Test Form';
				break;
		}
		
		// init tags data
		$params = new JRegistry();
		// trigger before event
		$dispatcher->trigger('onContentBeforeDisplay', array('com_jinbound.email', &$result, &$params, 0));
		// parse tags
		$htmlbody  = JInboundModelEmails::_replaceTags($htmlbody, $result, $tags);
		$plainbody = JInboundModelEmails::_replaceTags($plainbody, $result, $tags);
		// trigger after event
		$dispatcher->trigger('onContentAfterDisplay', array('com_jinbound.email', &$result, &$params, 0));
		// send
		$mail = JFactory::getMailer();
		$mail->ClearAllRecipients();
		$mail->SetFrom($fromemail, $fromname);
		$mail->addRecipient($to, 'Test Email');
		$mail->setSubject($subject);
		$mail->setBody($htmlbody);
		$mail->IsHTML(true);
		$mail->AltBody = $plainbody;
		$result = $mail->Send();
		if ($result instanceof Exception)
		{
			throw $result;
		}
		if (empty($result))
		{
			throw new Exception('Cannot send email');
		}
		echo 'Done';
		jexit();
	}
	
	public function edit($key = 'id', $urlVar = 'id') {
		if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound.email')) {
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		return parent::edit($key, $urlVar);
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'set') {
		$set     = JFactory::getApplication()->input->get('set', 'a', 'cmd');
		$append  = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append .= '&set=' . $set;
		return $append;
	}
}
