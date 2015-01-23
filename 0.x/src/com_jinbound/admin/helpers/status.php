<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

abstract class JInboundHelperStatus
{
	static public function getDefaultStatus()
	{
		static $default;
		
		if (is_null($default))
		{
			$db = JFactory::getDbo();
			
			$default = $db->setQuery($db->getQuery(true)
					->select($db->quoteName('id'))
					->from('#__jinbound_lead_statuses')
					->where($db->quoteName('default') . ' = 1')
					->where($db->quoteName('published') . ' = 1')
			)->loadResult();
			
			if (is_null($default))
			{
				$default = false;
			}
		}
		
		return $default;
	}
	
	static public function getFinalStatus()
	{
		static $final;
		
		if (is_null($final))
		{
			$db = JFactory::getDbo();
			
			$final = $db->setQuery($db->getQuery(true)
					->select($db->quoteName('id'))
					->from('#__jinbound_lead_statuses')
					->where($db->quoteName('final') . ' = 1')
					->where($db->quoteName('published') . ' = 1')
			)->loadResult();
			
			if (is_null($final))
			{
				$final = false;
			}
		}
		
		return $final;
	}
	
	static public function setContactStatusForCampaign($status_id, $contact_id, $campaign_id, $user_id = null)
	{
		$dispatcher = JDispatcher::getInstance();
		$db   = JFactory::getDbo();
		$date = JFactory::getDate()->toSql();
		// before we save the status, check if this user has this status already - don't duplicate the last status!
		$current = $db->setQuery($db->getQuery(true)
			->select($db->quoteName('status_id'))
			->from('#__jinbound_contacts_statuses')
			->where($db->quoteName('campaign_id') . '=' . $db->quote($campaign_id))
			->where($db->quoteName('contact_id') . '=' . $db->quote($contact_id))
			->order($db->quoteName('created') . ' DESC')
		)->loadResult();
		if ((int) $current === (int) $status_id)
		{
			// just pretend lol
			return true;
		}
		// save the status
		$value = $db->setQuery($db->getQuery(true)
			->insert('#__jinbound_contacts_statuses')
			->columns(array(
				'status_id'
			,	'campaign_id'
			,	'contact_id'
			,	'created'
			,	'created_by'
			))
			->values($db->quote($status_id)
			. ', ' . $db->quote($campaign_id)
			. ', ' . $db->quote($contact_id)
			. ', ' . $db->quote($date)
			. ', ' . $db->quote(JFactory::getUser($user_id)->get('id'))
			)
		)->query();
		
		$result = $dispatcher->trigger('onJInboundChangeState', array(
			'com_jinbound.contact.status', $campaign_id, array($contact_id), $status_id)
		);
		
		if (is_array($result) && !empty($result) && in_array(false, $result, true)) {
			return false;
		}
		
		return $value;
	}
}