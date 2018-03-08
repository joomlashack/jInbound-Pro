<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewPriorities extends JInboundListView
{
    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'Priority.name'        => JText::_('COM_JINBOUND_NAME')
        ,
            'Priority.published'   => JText::_('COM_JINBOUND_PUBLISHED')
        ,
            'Priority.ordering'    => JText::_('JGRID_HEADING_ORDERING')
        ,
            'Priority.description' => JText::_('COM_JINBOUND_DESCRIPTION')
        );
    }
}
