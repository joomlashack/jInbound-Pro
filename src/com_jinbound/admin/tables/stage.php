<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$e = new Exception(__FILE__);
JLog::add('JInboundTableStage is deprecated. ' . $e->getTraceAsString(), JLog::WARNING, 'deprecated');

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundTable', 'table');

class JInboundTableStage extends JInboundTable
{

    function __construct(&$db)
    {
        parent::__construct('#__jinbound_stages', 'id', $db);
    }

    /**
     * Redefined asset name, as we support action control
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_jinbound.stage.' . (int)$this->$k;
    }

    /**
     * We provide our global ACL as parent
     *
     * @see JTable::_getAssetParentId()
     */
    protected function _compat_getAssetParentId($table = null, $id = null)
    {
        $asset = JTable::getInstance('Asset');
        $asset->loadByName('com_jinbound');
        return $asset->id;
    }
}
