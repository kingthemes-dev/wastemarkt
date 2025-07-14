<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

$url   = urlencode( get_permalink() );
$title = urlencode( get_the_title() );

$defaults = array(
	'facebook' => array(
		'url'  => "http://www.facebook.com/sharer.php?u=$url",
		'icon' => 'fab fa-facebook-f',
	),
	'twitter'  => array(
		'url'  => "https://twitter.com/intent/tweet?source=$url&text=$title:$url",
		'icon' => 'fa-brands fa-x-twitter' 
	),
	'linkedin' => array(
		'url'  => "http://www.linkedin.com/shareArticle?mini=true&url=$url&title=$title",
		'icon' => 'fa-brands fa-linkedin-in'
	),
	'pinterest'=> array(
		'url'  => "http://pinterest.com/pin/create/button/?url=$url&description=$title",
		'icon' => 'fab fa-pinterest'
	),
	'tumblr'   => array(
		'url'  => "http://www.tumblr.com/share?v=3&u=$url &quote=$title",
		'icon' => 'fab fa-tumblr'
	),
	'reddit'   => array(
		'url'  => "http://www.reddit.com/submit?url=$url&title=$title",
		'icon' => 'fab fa-reddit-alien'
	),
	'vk'       => array(
		'url'  => "http://vkontakte.ru/share.php?url=$url",
		'icon' => 'fab fa-vk'
	),
);

foreach ( RDTheme::$options['post_share'] as $key => $value ) {
	if ( !$value ) {
		unset( $defaults[$key] );
	}
}

$sharers = apply_filters( 'rdtheme_social_sharing_icons', $defaults );
?>
<ul class="post-social-sharing">
	<?php foreach ( $sharers as $key => $sharer ): 
		$icon = $sharer['icon'];
	    $a = explode(' ',$icon);
	    $b = explode('-',$icon);
	?>
		<li class="social-<?php echo esc_attr($b[1]); ?>"><a href="<?php echo esc_url( $sharer['url'] );?>" target="_blank"><i class="<?php echo esc_attr( $sharer['icon'] );?>"></i></a></li>
	<?php endforeach; ?>
</ul>