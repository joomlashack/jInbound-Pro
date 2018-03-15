<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 **********************************************
 * jInbound
 * Copyright (c) 2013 Anything-Digital.com
 * Copyright (c) 2018 Open Source Training, LLC
 **********************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.n *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 */

defined('JPATH_PLATFORM') or die;

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