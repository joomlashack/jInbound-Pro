<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundListModel', 'models/basemodellist');

/**
 * This models supports retrieving lists of tracks.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelTracks extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.tracks';
	
	/**
	 * Constructor.
	 *
	 * @param       array   An optional associative array of configuration settings.
	 * @see         JController
	 */
	function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'Track.id'
			,	'Track.cookie'
			,	'Track.user_agent'
			,	'Track.created'
			,	'Track.ip'
			,	'Track.session_id'
			,	'Track.type'
			,	'Track.url'
			);
		}
		
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
		
		$app    = JFactory::getApplication();
		$format = $app->input->get('format', '', 'cmd');
		$end    = ('json' == $format ? '.json' : '');
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.start'.$end, 'filter_start', '', 'string');
		$this->setState('filter.start', $value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.end'.$end, 'filter_end', '', 'string');
		$this->setState('filter.end', $value);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.serialize($this->getState('filter.start'));
		$id	.= ':'.serialize($this->getState('filter.end'));

		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		
		// select columns
		$query = $db->getQuery(true)
			->select('Track.*')
			->from('#__jinbound_tracks AS Track')
		;
		
		// filter query
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Track', 'id', array(
			'id', 'cookie', 'user_agent', 'url', 'ip'
		));
		
		$value = $this->getState('filter.start');
		if (!empty($value)) {
			try {
				$date = new DateTime($value);
			}
			catch (Exception $e) {
				$date = false;
			}
			if ($date) {
				$query->where('Track.created > ' . $db->quote($date->format('Y-m-d h:i:s')));
			}
		}
		
		$value = $this->getState('filter.end');
		if (!empty($value)) {
			try {
				$date = new DateTime($value);
			}
			catch (Exception $e) {
				$date = false;
			}
			if ($date) {
				$query->where('Track.created < ' . $db->quote($date->format('Y-m-d h:i:s')));
			}
		}
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Track.created');
		$listDirn     = $db->escape($this->getState('list.direction', 'DESC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}
}