<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<script> 
const CONF_ZOOM_VERSION = '<?php echo CONF_ZOOM_VERSION; ?>';
</script>
<script src="https://source.zoom.us/<?php echo CONF_ZOOM_VERSION ?>/lib/vendor/react.min.js"></script>
<script src="https://source.zoom.us/<?php echo CONF_ZOOM_VERSION ?>/lib/vendor/react-dom.min.js"></script>
<script src="https://source.zoom.us/<?php echo CONF_ZOOM_VERSION ?>/lib/vendor/redux.min.js"></script>
<script src="https://source.zoom.us/<?php echo CONF_ZOOM_VERSION ?>/lib/vendor/redux-thunk.min.js"></script>
<script src="https://source.zoom.us/<?php echo CONF_ZOOM_VERSION ?>/lib/vendor/lodash.min.js"></script>
<script src="https://source.zoom.us/zoom-meeting-<?php echo CONF_ZOOM_VERSION ?>.min.js"></script>
<script src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('JsCss', 'js', [], '', false) . '?f=' . rawurlencode('zoom/page-js/tool.js') . '&min=0&sid=' . time(), CONF_DEF_CACHE_TIME, '.js'); ?>"></script>
<script src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('JsCss', 'js', [], '', false) . '?f=' . rawurlencode('zoom/page-js/vconsole.min.js') . '&min=0&sid=' . time(), CONF_DEF_CACHE_TIME, '.js'); ?>"></script>
<script src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('JsCss', 'js', [], '', false) . '?f=' . rawurlencode('zoom/page-js/meeting.js') . '&min=0&sid=' . time(), CONF_DEF_CACHE_TIME, '.js'); ?>"></script>