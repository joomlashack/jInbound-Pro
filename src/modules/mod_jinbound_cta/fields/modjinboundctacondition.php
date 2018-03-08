<?php
/**
 * @package             JInbound
 * @subpackage          mod_jinbound_cta
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');

if (!class_exists('ModJInboundCTAHelper')) {
    if (!file_exists($helper = JPATH_ROOT . '/modules/mod_jinbound_cta/helper.php')) {
        return false;
    }
    require_once $helper;
}

// load required classes
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/jinbound.php');
JInbound::registerHelper('url');

class JFormFieldModJInboundCTACondition extends JFormField
{
    public $type = 'ModJInboundCTACondition';

    protected function getInput()
    {
        $this->insertScript();
        $html = array();
        // start main element
        $html[] = '<div id="' . $this->id . '">';
        // add new buttons
        $html[]  = '<div id="' . $this->id . '_buttons" class="btn-group">';
        $buttons = ModJInboundCTAHelper::getButtonData();
        foreach (array('isnew', 'status', 'priority', 'campaign') as $button) {
            $html[] = $this->getButton("MOD_JINBOUND_CTA_$button", array_merge($buttons[$button], array(
                'group' => $this->fieldname
            )));
        }
        $html[] = '</div>';
        // start rendered inputs
        $html[] = '<div id="' . $this->id . '_controls" class="condition_controls">';
        $html[] = '</div>';
        // close main element
        $html[] = '</div>';
        return implode($html);
    }

    protected function insertScript()
    {
        // more than one field may load this script, so only load once
        global $mod_jinbound_cta_script_loaded;
        // only this field cares about these
        static $mod_jinbound_cta_css_loaded;
        static $mod_jinbound_cta_url_set;
        // init
        $doc = JFactory::getDocument();
        // check that this document type supports scripting
        if (!method_exists($doc, 'addScript')) {
            return;
        }
        // only add main script once
        if (is_null($mod_jinbound_cta_script_loaded)) {
            if (!JInbound::version()->isCompatible('3.0.0')) {
                // force jQuery
                $doc->addScript(JInboundHelperUrl::media() . '/js/jquery-1.9.1.min.js');
            }
            $doc->addScript(JUri::root() . 'media/mod_jinbound_cta/js/admin.js');
            $mod_jinbound_cta_script_loaded = true;
        }
        if (is_null($mod_jinbound_cta_css_loaded)) {
            $doc->addStyleSheet(JUri::root() . 'media/mod_jinbound_cta/css/modjinboundctacondition.css');
            $mod_jinbound_cta_css_loaded = true;
        }
        if (is_null($mod_jinbound_cta_url_set)) {
            $option = 'com_ajax';
            if (!JInbound::version()->isCompatible('3.0.0')) {
                $option = 'com_jinbound&task=ajax';
            }
            $url = JURI::root(false) . 'index.php?option=' . $option . '&module=jinbound_cta&method=getField&format=json';
            $doc->addScriptDeclaration("window.ModJInboundCTAConditionURL = '$url';");
            JFactory::getLanguage()->load('com_ajax', JPATH_ROOT);
            JText::script('COM_AJAX_MODULE_NOT_ACCESSIBLE');
            JText::script('MOD_JINBOUND_CTA_PUBLISH_AND_SAVE_FIRST');
        }
        $value = is_string($this->value) ? $this->value : json_encode($this->value);
        if (empty($value)) {
            $value = '{}';
        }
        $defaults = json_encode(ModJInboundCTAHelper::getDefaultEmptyFields($this->value));
        if (empty($defaults)) {
            $defaults = '[]';
        }
        $doc->addScriptDeclaration("jQuery(document).ready(function(){"
            . "window.ModJInboundCTAConditionURLs = $defaults;"
            . "window.ModJInboundCTACondition('{$this->id}', {$value});"
            . "});");
    }

    protected function getButton($text, $data = array())
    {
        $attributes = array();
        foreach ($data as $key => $value) {
            $attributes[] = 'data-' . $key . '="' . $value . '"';
        }
        $button = '<button class="btn jgrid" type="button" %s> <span class="icon-save-new state icon-16-newarticle"></span> %s </button>';
        return sprintf($button, implode(' ', $attributes), JText::_($text));
    }
}
