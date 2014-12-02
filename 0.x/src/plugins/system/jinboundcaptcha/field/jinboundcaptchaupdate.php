<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

class JFormFieldJinboundcaptchaupdate extends JFormField
{
	protected function getInput()
	{
		// rewritten LiveUpdate code
		require_once JPATH_ROOT . '/plugins/system/jinboundcaptcha/liveupdate/liveupdate.php';
		$updateInfo = LiveUpdate::getUpdateInformation();
		if (!$updateInfo->supported) {
			return JText::_('PLG_SYSTEM_JINBOUNDCAPTCHA_UPDATE_UNSUPPORTED');
		}
		else if ($updateInfo->stuck) {
			return JText::_('PLG_SYSTEM_JINBOUNDCAPTCHA_UPDATE_STUCK');
		}
		else if ($updateInfo->hasUpdates) {
			return JText::sprintf('PLG_SYSTEM_JINBOUNDCAPTCHA_UPDATE_HASUPDATES', $updateInfo->version);
		}
		return JText::_('PLG_SYSTEM_JINBOUNDCAPTCHA_UPDATE_OK');
	}
}
