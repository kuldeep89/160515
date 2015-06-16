<?php

require_once('LiveChatHelper.class.php');

class TrackingCodeHelper extends LiveChatHelper
{
	public function render()
	{
		if (LiveChat::get_instance()->is_installed())
		{
			$skill = LiveChat::get_instance()->get_skill();
			$license_number = LiveChat::get_instance()->get_license_number();

			return <<<HTML
<script type="text/javascript">
  var __lc = {};
  __lc.license = {$license_number};
  __lc.group = {$skill};

  (function() {
    var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
    lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
  })();
</script>
HTML;
		}

		return '';
	}
}