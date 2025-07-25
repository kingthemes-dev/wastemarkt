<?php
    if ( !defined('ABSPATH' ) )
        exit();

    $machine_translation_settings = $trp_settings->get_setting('trp_machine_translation_settings');
    $show_formality               = isset( $machine_translation_settings ) && $machine_translation_settings['machine-translation'] === 'yes' && ( $machine_translation_settings['translation-engine'] === 'deepl' || $machine_translation_settings['translation-engine'] === 'mtapi' );
?>

<div class="trp-languages-table__wrapper">
    <table id="trp-languages-table">
        <thead>
        <tr>
            <th class="trp-languages-table-heading-item trp-primary-text-bold">
                <span><?php esc_html_e( 'All Languages', 'translatepress-multilingual' ); ?></span>
                <div class="trp-settings-info-sign" data-tooltip="<?php echo wp_kses( __( 'Select the languages you wish to make your website available in.', 'translatepress-multilingual' ), array() ); ?> "></div>
            </th>
            <?php if( $show_formality ){ ?>
                <th class="trp-languages-table-heading-item trp-primary-text-bold trp-languages-table-heading-item__indented">
                    <span><?php esc_html_e( 'Formality', 'translatepress-multilingual' ); ?></span>
                    <div class="trp-settings-info-sign" data-tooltip="<?php echo wp_kses( __( 'The Formality field is used by Automatic Translation to decide whether the translated text should lean towards formal or informal language. For now, it is supported only for a few languages and only by DeepL.', 'translatepress-multilingual' ), array() ); ?>"></div>
                </th>
            <?php } ?>
            <th class="trp-languages-table-heading-item trp-primary-text-bold"><?php esc_html_e( 'Code', 'translatepress-multilingual' ); ?></th>
            <th class="trp-languages-table-heading-item trp-primary-text-bold trp-languages-table-heading-item__indented"><?php esc_html_e( 'Slug', 'translatepress-multilingual' ); ?></th>
            <th class="trp-languages-table-heading-item trp-primary-text-bold">
                <span><?php esc_html_e( 'Active', 'translatepress-multilingual' ); ?></span>
                <div class="trp-settings-info-sign" data-tooltip="<?php echo wp_kses( __( 'The inactive languages will still be visible and active for the admin. For other users they won\'t be visible in the language switchers and won\'t be accessible either.', 'translatepress-multilingual' ), array() ); ?>"></div>
            </th>
        </tr>
        </thead>
        <tbody id="trp-sortable-languages">

        <?php
        $formality_array = array(
            'default' => __('Default', 'translatepress-multilingual'),
            'formal'  => __('Formal', 'translatepress-multilingual'),
            'informal'=> __('Informal', 'translatepress-multilingual')
        );

        $data = get_option('trp_db_stored_data', array() );
        $formality_supported_languages = $show_formality && isset( $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine']] ) ? $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine']]['formality-supported-languages'] : null;
        ?>
        <?php foreach ( $this->settings['translation-languages'] as $key=>$selected_language_code ){
            $default_language            = ( $selected_language_code == $this->settings['default-language'] );
            $language_supports_formality = isset( $formality_supported_languages ) ? isset( $formality_supported_languages[$selected_language_code] ) && $formality_supported_languages[$selected_language_code]  === 'true' : null;
            $stripped_formal_language    = isset( $formality_supported_languages[ str_replace( ['_formal','_informal'],'', $selected_language_code ) ] ) ? $formality_supported_languages[ str_replace( ['_formal','_informal'],'', $selected_language_code ) ] : false;
            $formality_disabled          = false;
        ?>
            <tr class="trp-language">
                <td class="trp-col-select-language">
                    <div class="trp-sortable-language__wrap">
                        <div class="trp-sortable-handle">
                            <svg width="11" height="18" viewBox="0 0 11 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M2 14C3.1 14 4 14.9 4 16C4 17.1 3.1 18 2 18C0.9 18 -3.93393e-08 17.1 -8.74219e-08 16C-1.35504e-07 14.9 0.9 14 2 14ZM-6.99381e-07 2C-6.51299e-07 3.1 0.899999 4 2 4C3.1 4 4 3.1 4 2C4 0.899999 3.1 -1.35505e-07 2 -8.74228e-08C0.899999 -3.93403e-08 -7.47464e-07 0.9 -6.99381e-07 2ZM2 11C0.9 11 -3.4532e-07 10.1 -3.93402e-07 9C-4.41485e-07 7.9 0.899999 7 2 7C3.1 7 4 7.9 4 9C4 10.1 3.1 11 2 11Z" fill="#9CA1A8"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 14C10.1 14 11 14.9 11 16C11 17.1 10.1 18 9 18C7.9 18 7 17.1 7 16C7 14.9 7.9 14 9 14ZM7 2C7 3.1 7.9 4 9 4C10.1 4 11 3.1 11 2C11 0.899999 10.1 -1.35505e-07 9 -8.74228e-08C7.9 -3.93403e-08 7 0.9 7 2ZM9 11C7.9 11 7 10.1 7 9C7 7.9 7.9 7 9 7C10.1 7 11 7.9 11 9C11 10.1 10.1 11 9 11Z" fill="#9CA1A8"/>
                            </svg>
                        </div>
                        <select name="trp_settings[translation-languages][]" class="trp-select2 trp-translation-language" <?php echo ( $default_language ) ? 'disabled' : '' ?>>
                            <?php foreach( $languages as $language_code => $language_name ){ ?>
                                <option title="<?php echo esc_attr( $language_code ); ?>" value="<?php echo esc_attr( $language_code ); ?>" <?php echo ( $language_code == $selected_language_code ) ? 'selected' : ''; ?>>
                                    <?php echo ( $default_language ) ? 'Default: ' : ''; ?>
                                    <?php echo esc_html( $language_name ); ?>
                                </option>
                            <?php }?>
                        </select>
                    </div>
                </td>
                <?php if( $show_formality ){
                    $formality_disabled = !$language_supports_formality && !$stripped_formal_language || $stripped_formal_language === 'false';
                ?>
                    <td class="trp-col-formality" <?php echo $formality_disabled ? 'title="' . esc_attr__( "This language does not support formality. ", 'translatepress-multilingual' ) . '"' : ''; ?>>
                        <select name="trp_settings[translation-languages-formality][]" class="trp-translation-language-formality <?php echo $formality_disabled ? 'trp-formality-disabled' : ''; ?>">
                            <?php
                            foreach ( $formality_array as $value => $label ) {
                                ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php echo ( isset($this->settings['translation-languages-formality-parameter'][$selected_language_code]) && $value == $this->settings['translation-languages-formality-parameter'][$selected_language_code] ) ? 'selected' : ''; ?>><?php echo esc_html( $label ); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                <?php } ?>
                <td class="trp-col-language-code">
                    <input class="trp-language-code trp-code-slug" type="text" disabled value="<?php echo esc_html( $selected_language_code ); ?>">
                </td>
                <td class="trp-col-language-slug">
                    <input class="trp-language-slug  trp-code-slug" name="trp_settings[url-slugs][<?php echo esc_attr( $selected_language_code ) ?>]" type="text" style="text-transform: lowercase;" value="<?php echo esc_attr( $this->url_converter->get_url_slug( $selected_language_code, false ) ); ?>">
                </td>
                <td class="trp-col-active-language">
                    <div class="trp-switch">
                        <input type="checkbox" id="switch-<?php echo esc_attr($selected_language_code); ?>"
                               class="trp-switch-input trp-translation-published"
                               name="trp_settings[publish-languages][]"
                               value="<?php echo esc_attr($selected_language_code); ?>"
                            <?php echo in_array($selected_language_code, $this->settings['publish-languages']) ? 'checked ' : ''; ?>
                            <?php echo $default_language ? 'disabled ' : ''; ?> />

                        <label for="switch-<?php echo esc_attr($selected_language_code); ?>" class="trp-switch-label"></label>

                        <?php if ($default_language) { ?>
                            <input type="hidden" class="trp-hidden-default-language"
                                   name="trp_settings[translation-languages][]"
                                   value="<?php echo esc_attr($selected_language_code); ?>" />
                            <input type="hidden" class="trp-hidden-default-language"
                                   name="trp_settings[publish-languages][]"
                                   value="<?php echo esc_attr($selected_language_code); ?>" />
                        <?php } ?>
                    </div>
                </td>
                <td class="trp-col-remove-language">
                    <div class="trp-remove-language__container" style=" <?php echo ( $default_language ) ? 'display:none' : '' ?>">
                        <a class="trp-remove-language" data-confirm-message="<?php esc_html_e( 'Are you sure you want to remove this language?', 'translatepress-multilingual' ); ?>"><?php esc_html_e( 'Remove', 'translatepress-multilingual' ); ?></a>
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 4.5H15C15.6 4.5 16 4.9 16 5.5V6.5H3V5.5C3 4.9 3.5 4.5 4 4.5H7C7.2 3.4 8.3 2.5 9.5 2.5C10.7 2.5 11.8 3.4 12 4.5ZM11 4.5C10.8 3.9 10.1 3.5 9.5 3.5C8.9 3.5 8.2 3.9 8 4.5H11ZM14.1 17.6L15 7.5H4L4.9 17.6C5 18.1 5.4 18.5 5.9 18.5H13.1C13.6 18.5 14.1 18.1 14.1 17.6Z" fill="#757575"/>
                        </svg>
                    </div>
                </td>
            </tr>
        <?php }?>

        </tbody>
    </table>
    <div id="trp-new-language">
        <div style="width: 31px; height: 1px;"></div>
        <select id="trp-select-language" class="trp-select2 trp-translation-language" >
            <?php
            $trp = TRP_Translate_Press::get_trp_instance();
            $trp_languages = $trp->get_component('languages');
            $wp_languages = $trp_languages->get_wp_languages();
            ?>
            <option value=""><?php esc_html_e( 'Select language', 'translatepress-multilingual' );?></option>
            <?php foreach( $languages as $language_code => $language_name ){ ?>

        <?php if(isset($wp_languages[$language_code]['is_custom_language']) && $wp_languages[$language_code]['is_custom_language'] === true){?>
            <optgroup label="<?php echo esc_html__('Custom Languages', 'translatepress-multilingual'); ?>">
                <?php break;?>
                <?php } ?>
                <?php } ?>
                <?php foreach( $languages as $language_code => $language_name ){ ?>

                    <?php if(isset($wp_languages[$language_code]['is_custom_language']) && $wp_languages[$language_code]['is_custom_language'] === true){ ?>
                        <option title="<?php echo esc_attr( $language_code ); ?>" value="<?php echo esc_attr( $language_code ); ?>">
                            <?php echo esc_html( $language_name ); ?>
                        </option>

                    <?php } ?>

                <?php }?>
            </optgroup>
            <?php foreach( $languages as $language_code => $language_name ){ ?>
                <?php if(!isset($wp_languages[$language_code]['is_custom_language']) || (isset($wp_languages[$language_code]['is_custom_language']) && $wp_languages[$language_code]['is_custom_language'] !== true)){ ?>
                    <option title="<?php echo esc_attr( $language_code ); ?>" value="<?php echo esc_attr( $language_code ); ?>">
                        <?php echo esc_html( $language_name ); ?>

                    </option>
                <?php } ?>
            <?php }?>
        </select>
        <button type="button" id="trp-add-language" class="trp-button-secondary"><?php esc_html_e( 'Add', 'translatepress-multilingual' );?></button>
    </div>
</div>

<p class="trp-add-language-error-container trp-settings-warning" style="display: none;"></p>