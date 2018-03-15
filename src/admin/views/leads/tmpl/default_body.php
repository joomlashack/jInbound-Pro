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

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Lead.id');
$trashed   = (-2 == $this->state->get('filter.published'));

if (JInbound::version()->isCompatible('3.0')) {
    JHtml::_('dropdown.init');
}

JHtml::_('jinbound.leadupdate');


if (!empty($this->items)) :
    foreach ($this->items as $i => $item) :
        $this->_itemNum = $i;

        $canEdit    = $user->authorise('core.edit', JInbound::COM . '.lead.' . $item->id);
        $canCheckin = $user->authorise('core.manage',
                'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
        $canEditOwn = $user->authorise('core.edit.own',
                JInbound::COM . '.lead.' . $item->id) && $item->created_by == $userId;
        $canChange  = $user->authorise('core.edit.state', JInbound::COM . '.lead.' . $item->id) && $canCheckin;
        ?>
        <tr class="row<?php echo $i % 2; ?>">
            <td class="hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
            </td>
            <td class="nowrap has-context">
                <div class="pull-left">
                    <?php if ($item->checked_out) : ?>
                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->author_name, $item->checked_out_time,
                            'leads.', $canCheckin); ?>
                    <?php endif; ?>
                    <?php if ($canEdit || $canEditOwn) : ?>
                        <a href="<?php echo JInboundHelperUrl::edit('lead', $item->id); ?>">
                            <?php echo $this->escape($item->name); ?>
                        </a>
                    <?php else : ?>
                        <?php echo $this->escape($item->name); ?>
                    <?php endif; ?>
                </div>
                <?php $this->currentItem = $item;
                echo $this->loadTemplate('list_dropdown'); ?>
            </td>
            <td class="hidden-phone">
                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'leads.', $canChange, 'cb'); ?>
            </td>
            <td class="hidden-phone hidden-tablet">
                <?php echo $this->escape($item->created); ?>
            </td>
            <td class="hidden-phone hidden-tablet">
                <?php echo JHtml::_('jinbound.formdata', $item->id, $item->formname, $item->formdata, 'leads.',
                    $canChange); ?>
            </td>
            <td class="hidden-phone hidden-tablet">
                <?php echo JHtml::_('jinbound.priority', $item->id, $item->priority_id, 'leads.', $canChange); ?>
            </td>
            <td class="hidden-phone hidden-tablet">
                <?php echo $this->escape($item->campaign_name); ?>
            </td>
            <td class="hidden-phone hidden-tablet">
                <?php echo JHtml::_('jinbound.status', $item->id, $item->status_id, 'leads.', $canChange); ?>
            </td>
            <td class="hidden-phone hidden-tablet">
                <?php echo JHtml::_('jinbound.leadnotes', $item->id, $canChange); ?>
            </td>
            <td class="hidden-phone">
                <?php echo $item->id; ?>
            </td>
        </tr>
    <?php endforeach;
endif;