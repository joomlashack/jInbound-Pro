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

class JInboundListView extends JInboundView
{
    /**
     * @var object
     */
    protected $items;

    /**
     * @var JPagination
     */
    protected $pagination;

    /**
     * @var JObject
     */
    protected $state;

    protected $ordering = null;

    protected $permissions = null;

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * @var string
     */
    protected $currentFilter = null;

    public $filterForm = null;

    public $activeFilters = null;

    /**
     * @param string $tpl
     *
     * @return void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()
            ->authorise('core.manage', 'com_jinbound.' . strtolower(JInboundInflector::singularize($this->_name)))) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->permissions   = $this->get('Permissions');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode('<br />', $errors), 500);
        }

        $this->ordering = array(0 => array());
        if (!empty($this->items)) {
            foreach ($this->items as $item) {
                if (!(property_exists($item, 'ordering') || property_exists($item, 'lft'))) {
                    break;
                }
                $this->ordering[0][] = $item->id;
            }
        }

        $publishedOptions = $this->get('PublishedStatus');
        if (!empty($publishedOptions)) {
            $this->addFilter(
                JText::_('COM_JINBOUND_SELECT_PUBLISHED'),
                'filter[published]',
                $publishedOptions,
                $this->state->get('filter.published')
            );
        }

        parent::display($tpl);
    }

    public function addFilter($label, $name, $options, $default, $trim = true)
    {
        $filter = (object)array(
            'label'   => $label,
            'name'    => $name,
            'options' => $options,
            'default' => $default,
            'trim'    => $trim
        );

        $this->filters[] = $filter;

        return $this->filters;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function renderFilters()
    {
        if (empty($this->filters)) {
            return '';
        }
        if (class_exists('JHtmlSidebar')) {
            foreach ($this->filters as $filter) {
                if (empty($filter->options)) {
                    continue;
                }
                if ($filter->trim) {
                    array_shift($filter->options);
                }
                $options = JHtml::_('select.options', $filter->options, 'value', 'text', $filter->default, true);
                JHtmlSidebar::addFilter($filter->label, $filter->name, $options);
            }
            return '';
        }

        $html = array();
        foreach ($this->filters as $filter) {
            if (empty($filter->options)) {
                continue;
            }

            $this->currentFilter = JHtml::_(
                'select.genericlist',
                $filter->options,
                $filter->name,
                sprintf('id="%s" class="listbox" onchange="this.form.submit()"', $filter->name),
                'value',
                'text',
                $filter->default
            );

            $html[] = $this->loadTemplate('filter', 'default');
        }

        return implode("\n", $html);
    }

    /**
     * @throws Exception
     */
    public function addToolBar()
    {
        // only fire in administrator, and only once
        if (!JFactory::getApplication()->isAdmin()) {
            return;
        }

        static $set;

        if (is_null($set)) {
            $single       = strtolower(JInboundInflector::singularize($this->_name));
            $user         = JFactory::getUser();
            $canCreate    = $user->authorise('core.create', JInbound::COM . ".$single");
            $canDelete    = $user->authorise('core.delete', JInbound::COM . ".$single");
            $canEdit      = $user->authorise('core.edit', JInbound::COM . ".$single");
            $canEditOwn   = $user->authorise('core.edit.own', JInbound::COM . ".$single");
            $canEditState = $user->authorise('core.edit.state', JInbound::COM . ".$single");
            // set the toolbar title
            $title = strtoupper(JInbound::COM . '_' . $this->_name . '_MANAGER');
            $class = 'jinbound-' . strtolower($this->_name);
            if ('contacts' === $this->_name) {
                $title = strtoupper(JInbound::COM . '_LEADS_MANAGER');
                $class = 'jinbound-leads';
            }
            if ($canCreate) {
                JToolBarHelper::addNew($single . '.add', 'JTOOLBAR_NEW');
            }
            if ($canEdit || $canEditOwn) {
                JToolBarHelper::editList($single . '.edit', 'JTOOLBAR_EDIT');
                JToolBarHelper::divider();
            }
            if ($canEditState) {
                JToolBarHelper::publish($this->_name . '.publish', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::unpublish($this->_name . '.unpublish', 'JTOOLBAR_UNPUBLISH', true);
                JToolBarHelper::checkin($this->_name . '.checkin');
                JToolBarHelper::divider();
            }
            if ($this->state->get('filter.published') == -2 && $canDelete) {
                JToolBarHelper::deleteList('', $this->_name . '.delete', 'JTOOLBAR_EMPTY_TRASH');
            } else {
                if ($canEditState) {
                    JToolBarHelper::trash($this->_name . '.trash');
                    JToolBarHelper::divider();
                }
            }
            // add parent toolbar
            parent::addToolBar();

            JToolBarHelper::title(JText::_($title), $class);
        }
        $set = true;
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array();
    }
}