<?php
/**
 * Represents the view for Facebook Like Box Widget.
 *
 * @package   Cherry Social
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @copyright 2012 - 2015, Cherry Team
 */
?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="fb-page"
	data-href="<?php echo $page_url; ?>"
	data-height="<?php echo $height; ?>"
	data-hide-cover="<?php echo $cover; ?>"
	data-small-header="<?php echo $header; ?>"
	data-show-facepile="<?php echo $faces; ?>"
	data-show-posts="<?php echo $posts; ?>"
	data-adapt-container-width="true">
</div>
