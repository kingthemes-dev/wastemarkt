<?php

use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

/**
 * @var string      $language
 * @var RtclMc_Data $settings
 * @var array       $classes
 */

$currency_selected = $settings->get_current_currency();
$title = $settings->get_design_title($language);

$style = '';
if ($max_height = $settings->get_param('max_height')) {
    $style = "max-height:{$max_height}px; overflow-y:auto;";
}
?>


<div class="rtclmc <?php echo esc_attr(implode(' ', $classes)); ?> rtclmc-bottom rtclmc-sidebar"
     style="<?php echo esc_html($style) ?>">
    <div class="rtclmc-list-currencies">
        <?php if ($title) { ?>
            <div class="rtclmc-title"><?php echo esc_html($title) ?></div>
        <?php }
        $links = $settings->get_links();
        $currency_name = Options::get_currency_list();
        foreach ($links as $k => $link) {
            $selected = $display = '';
            $k = esc_attr($k);

            if ($currency_selected == $k) {
                $selected = 'rtclmc-active';
            }
            switch ($settings->get_sidebar_style()) {
                case 1:
                    $symbol = esc_html(Functions::get_currency_symbol($k));
                    $display = "<span>{$symbol}</span>";
                    break;
                case 2:
                case 3:
                case 4:
                    $country = esc_html(strtolower($settings->get_country_data($k)['code']));
                    $display = "<span><i class='rtclmc-flag-64 flag-{$country}'></i></span>";
                    break;
                default:
                    $display = "<span>{$k}</span>";
            }
            ?>
            <div class="rtclmc-currency <?php echo esc_attr($selected) ?>"
                 data-currency="<?php echo esc_attr($k) ?>">
                <?php
                echo($display);
                $active = $selected ? "rtclmc-active-title" : '';
                $link = esc_url($link);
                echo $settings->enable_switch_currency_by_js() ?
                    "<a rel='nofollow' href='#' class='rtclmc-currency-redirect' data-currency='{$k}'>" : "<a rel='nofollow' href='{$link}' >";
                switch ($settings->get_sidebar_style()) {
                    case 3:
                        echo $k;
                        break;
                    case 4:
                        echo Functions::get_currency_symbol($k);
                        break;
                    default:
                        echo esc_html($currency_name[$k]);
                }
                echo '</a>';
                ?>
            </div>
        <?php } ?>
        <div class="rtclmc-sidebar-open"></div>
    </div>
</div>