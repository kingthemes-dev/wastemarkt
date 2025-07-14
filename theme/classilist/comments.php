<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

if ( post_password_required() ) {
    return;
}

if ( !have_comments() && !comments_open() ) {
    return;
}

$comments_number = number_format_i18n( get_comments_number() );
$comments_html   = sprintf( _n( '%s Comment', '%s Comments', $comments_number, 'classilist' ), number_format_i18n( $comments_number ) );

$rdtheme_commenter = wp_get_current_commenter();        
$rdtheme_req       = get_option( 'require_name_email' );
$rdtheme_aria_req  = ( $rdtheme_req ? " required" : '' );

$rdtheme_fields =  array(
    'author' =>
    '<div class="row"><div class="col-sm-4"><div class="form-group comment-form-author"><input type="text" id="author" name="author" value="' . esc_attr( $rdtheme_commenter['comment_author'] ) . '" placeholder="'.esc_attr__( 'Name', 'classilist' ).( $rdtheme_req ? ' *' : '' ).'" class="form-control"' . $rdtheme_aria_req . '></div></div>',

    'email' =>
    '<div class="col-sm-4 comment-form-email"><div class="form-group"><input id="email" name="email" type="email" value="' . esc_attr(  $rdtheme_commenter['comment_author_email'] ) . '" class="form-control" placeholder="'.esc_attr__( 'Email', 'classilist' ).( $rdtheme_req ? ' *' : '' ).'"' . $rdtheme_aria_req . '></div></div>',   

    'url' =>
    '<div class="col-sm-4 comment-form-website"><div class="form-group"><input id="website" name="website" type="text" value="' . esc_attr(  $rdtheme_commenter['comment_author_url'] ) . '" class="form-control" placeholder="'.esc_attr__( 'Website', 'classilist' ).( $rdtheme_req ? '' : '' ).'"' . $rdtheme_aria_req . '></div></div></div>',
);

$rdtheme_args = array(
    'class_submit'  => 'submit btn-send',
    'submit_field'  => '<div class="form-group form-submit">%1$s %2$s</div>',
    'comment_field' =>  '<div class="form-group comment-form-comment"><textarea id="comment" name="comment" required placeholder="'.esc_attr__( 'Comment *', 'classilist' ).'" class="textarea form-control" rows="10" cols="40"></textarea></div>',
    'fields' => apply_filters( 'comment_form_default_fields', $rdtheme_fields ),
);
?>

<div id="comments" class="comments-area">
    <?php if ( have_comments() ): ?>
        <h3 class="comment-title post-title-block"><?php echo esc_html( $comments_html );?></h3>
        <?php
        $rdtheme_avatar = get_option( 'show_avatars' );
        ?>
        <ul class="comment-list<?php echo empty( $rdtheme_avatar ) ? ' avatar-disabled' : '';?>">
            <?php
            wp_list_comments(
                array(
                    'style'        => 'ul',
                    'callback'     => 'radiustheme\ClassiList\Helper::comments_callback',
                    'reply_text'   => esc_html__( 'Reply', 'classilist' ),
                    'avatar_size'  => 100,
                    'format'       => 'html5'
                ) 
            );
            ?>
        </ul>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :?>
            <nav class="comment-pagination">
                <ul>
                    <li><?php previous_comments_link( esc_html__( 'Older Comments', 'classilist' ) ); ?></li>
                    <li><?php next_comments_link( esc_html__( 'Newer Comments', 'classilist' ) ); ?></li>
                </ul>
            </nav>
        <?php endif;?>

    <?php endif;?>

    <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
        <p class="comments-closed"><?php esc_html_e( 'Comments are closed.', 'classilist' ); ?></p>
    <?php endif;?>

    <?php
    if ( comments_open() ){
        comment_form( $rdtheme_args );
    }
    ?>
</div>