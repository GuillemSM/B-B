<?php
/**
 * Represents the view for Twitter Timeline Widget.
 *
 * @package   Cherry Social
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @copyright 2012 - 2015, Cherry Team
 */
?>
<a class="twitter-timeline"
	data-widget-id="<?php echo $tw_widget_id; ?>"
	data-theme="<?php echo $skin; ?>"
	data-link-color="#<?php echo $link_color; ?>"
	data-border-color="#<?php echo $border_color; ?>"
	data-tweet-limit="<?php echo $limit; ?>"
	data-chrome="<?php echo join( ' ', $chrome ); ?>"
	height="<?php echo $height; ?>">
</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
