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

JText::script('COM_JINBOUND_RESET_CONFIRM');

$user = JFactory::getUser();
foreach (array('campaign', 'email', 'page', 'contact', 'report') as $type) {
    foreach (array('manage', 'create') as $var) {
        ${"can" . str_replace(' ', '',
                ucwords(str_replace('.', ' ', $var))) . ucwords($type)} = $user->authorise("core.$var",
            JInbound::COM . ".$type");
    }
}
$jserror = "javascript:alert('" . JText::_('JERROR_ALERTNOAUTHOR') . "');";

?>
    <script type="text/javascript">
        Joomla.submitbutton = function(task) {
            if ('reset' === task && confirm(Joomla.JText._('COM_JINBOUND_RESET_CONFIRM'))) {
                Joomla.submitform(task, document.getElementById('adminForm'));
            }
        };
        <?php if (!empty($this->updates)) : ?>
        (function($, d) {
            var urls = [<?php
                $updates = array();
                foreach ($this->updates as $update) {
                    if (is_array($update)) {
                        foreach ($update as $url) {
                            $updates[] = "'" . JInboundHelperFilter::escape_js($url) . "'";
                        }
                    } else {
                        $updates[] = "'" . JInboundHelperFilter::escape_js($update) . "'";
                    }
                }
                echo implode(',', $updates);
                ?>];
            $(d).ready(function() {
                $.each(urls, function(i, url) {
                    $.ajax(url, {
                        success: function(response) {
                            if (!response.length) {
                                return;
                            }
                            $('<div class="alert alert-info">' + response + '</div>').insertBefore($('#adminForm'));
                        }
                    });
                });
            });
        })(jQuery, document);
        <?php endif; ?>
    </script>
    <form action="<?php echo JInboundHelperUrl::_(); ?>" method="post" id="adminForm" name="adminForm"
          class="form-validate" enctype="multipart/form-data">
        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>
    </form>
    <!-- Main Component container -->
    <div class="container-fluid" id="jinbound_component">
        <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
            <?php else : ?>
            <div id="j-main-container">
                <?php endif; ?>

                <!-- Main Dashboard columns -->
                <div class="row-fluid">
                    <!-- Left panel -->
                    <div class="span8">
                        <!-- Row 1 - Welcome Message-->
                        <div class="row-fluid" id="welcome_message">
                            <div class="span12">
                                <p class="lead"><?php echo JText::_('COM_JINBOUND_WELCOME_TO_JINBOUND'); ?></p>
                            </div>
                        </div>
                        <!-- Row 2 - Buttons -->
                        <div class="row-fluid" id="welcome_buttons">
                            <?php
                            $class = 'span3 btn text-center';
                            if ($canManageCampaign) {
                                $href = JInboundHelperUrl::view('campaigns');
                            } else {
                                $href  = $jserror;
                                $class .= ' disabled';
                            }
                            ?>
                            <a href="<?php echo $href; ?>" class="<?php echo $class; ?>">
                                <span
                                    class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_1_CREATE_A_CAMPAIGN'); ?></span>
                                <span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/lead_manager.png'; ?>"/>
	      	</span>
                            </a>
                            <?php
                            $class = 'span3 btn text-center';
                            if ($canManageEmail) {
                                $href = JInboundHelperUrl::view('emails');
                            } else {
                                $href  = $jserror;
                                $class .= ' disabled';
                            }
                            ?>
                            <a href="<?php echo $href; ?>" class="<?php echo $class; ?>">
                                <span
                                    class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_2_WRITE_EMAILS_FOR_YOUR_CAMPAIGN'); ?></span>
                                <span class="row text-center">
	      		<img class="img-rounded"
                     src="<?php echo JInboundHelperUrl::media() . '/images/leads_nurturing.png'; ?>"/>
	      	</span>
                            </a>
                            <?php
                            $class = 'span3 btn text-center';
                            if ($canManagePage) {
                                $href = JInboundHelperUrl::view('pages');
                            } else {
                                $href  = $jserror;
                                $class .= ' disabled';
                            }
                            ?>
                            <a href="<?php echo $href; ?>" class="<?php echo $class; ?>">
                                <span
                                    class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_3_CREATE_LANDING_PAGES_TO_GET_PEOPLE_INTO_YOUR_CAMPAIGN'); ?></span>
                                <span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/landing_pages.png'; ?>"/>
	      	</span>
                            </a>
                            <?php
                            $class = 'span3 btn text-center';
                            if ($canManageContact) {
                                $href = JInboundHelperUrl::view('contacts');
                            } else {
                                $href  = $jserror;
                                $class .= ' disabled';
                            }
                            ?>
                            <a href="<?php echo $href; ?>" class="<?php echo $class; ?>">
                                <span
                                    class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_4_GET_REPORTS_ON_PEOPLE_WHO_SIGNED_UP'); ?></span>
                                <span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/reports.png'; ?>"/>
	      	</span>
                            </a>
                        </div>

                        <?php if ($canManageReport) : ?>
                            <!-- Row 3 - Monthly Report -->
                            <div class="row-fluid">
                                <!-- start the container -->
                                <div class="well">
                                    <!-- Report Heading -->
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <h3 class="text-center"><?php echo JText::_('COM_JINBOUND_MONTHLY_REPORTING_SNAPSHOT'); ?></h3>
                                        </div>
                                    </div>
                                    <?php echo $this->reports->glance; ?>
                                    <?php
                                    $filter_start = new DateTime();
                                    $filter_end   = clone $filter_start;
                                    $filter_start->modify('-1 month');
                                    $filter_end->modify('+1 day');

                                    ?>
                                    <input id="filter_start" type="hidden"
                                           value="<?php echo $filter_start->format('Y-m-d'); ?>"/>
                                    <input id="filter_end" type="hidden"
                                           value="<?php echo $filter_end->format('Y-m-d'); ?>"/>
                                    <select id="filter_campaign" style="display:none">
                                        <option value=""></option>
                                    </select>
                                    <select id="filter_page" style="display:none">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <?php
                            echo $this->reports->top_pages;
                        endif;
                        if ($canManageContact) :
                            echo $this->reports->recent_leads;
                        endif;
                        ?>

                    </div>
                    <!-- Sidebar -->
                    <div class="span4 jinbound-dashboard-sidebar">
                        <?php if ($canManageReport) : ?>
                            <div class="well">
                                <h3><?php echo JHtml::link(JInboundHelperUrl::view('reports'),
                                        '<img alt="' . JText::_('COM_JINBOUND_VIEW_REPORTS') . '" src="' . JInboundHelperUrl::media() . '/images/view_reports.png" /> <span>' . JText::_('COM_JINBOUND_VIEW_REPORTS') . '</span>'); ?></h3>
                            </div>
                        <?php endif; ?>
                        <div class="well jinbound-rss jinbound-rss-news"> ...</div>
                        <div class="well jinbound-rss jinbound-rss-feed"> ...</div>
                    </div>

                </div>

            </div>
        </div>
        <script type="text/javascript">
            (function($) {
                // helper function to load rss async
                var loadRss = function(target) {
                    // find the rss target element
                    var well = $('.jinbound-dashboard-sidebar .jinbound-rss-' + target);
                    if (!well.length) {
                        return;
                    }
                    // url to ask for rss
                    var url = '<?php
                        $url = JRoute::_('index.php?option=com_jinbound&task=rss', false);
                        echo $url . (false === strpos($url, '?') ? '?' : '&') . 'type=';
                        ?>' + target;
                    // fetch the rss via ajax
                    $.ajax({
                        url    : url,
                        success: function(data) {
                            well.html(data);
                        }
                    });
                };
                $(document).ready(function() {
                    loadRss('news');
                    loadRss('feed');
                });
            })(jQuery);
        </script>
<?php

echo $this->loadTemplate('footer');

echo $this->reports->script;
