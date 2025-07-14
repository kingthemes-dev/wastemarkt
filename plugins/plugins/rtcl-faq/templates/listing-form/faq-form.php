<?php
/**
 * FAQ forms
 *
 * @var $faqs
 */
?>
<div class="form-group">
	<div class="rtcl-post-section-title">
		<h3>
		   <p class="rtcl-faq-title">
			   <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
				   <path d="M9 11.7017C8.58334 11.7017 8.30556 11.4234 8.30556 11.0059C8.37501 10.1012 8.86111 9.33574 9.55555 8.77903C9.97221 8.36151 10.3889 7.94398 10.3889 7.52645C10.3889 6.76098 9.76388 6.13469 9 6.13469C8.23612 6.13469 7.61113 6.76098 7.61113 7.52645C7.61113 7.94398 7.33335 8.22233 6.91669 8.22233C6.50003 8.22233 6.22225 7.94398 6.22225 7.52645C6.22225 5.99551 7.47224 4.74292 9 4.74292C10.5278 4.74292 11.7777 5.99551 11.7777 7.52645C11.7083 8.43109 11.2222 9.19656 10.5278 9.75327C10.1111 10.1708 9.69444 10.5883 9.69444 11.0059C9.69444 11.4234 9.41666 11.7017 9 11.7017Z" fill="currentColor"/>
				   <path d="M9 13.7894C9.38353 13.7894 9.69444 13.4778 9.69444 13.0935C9.69444 12.7092 9.38353 12.3976 9 12.3976C8.61647 12.3976 8.30556 12.7092 8.30556 13.0935C8.30556 13.4778 8.61647 13.7894 9 13.7894Z" fill="currentColor"/>
				   <path d="M0.94453 17.8255C0.736199 17.6863 0.666755 17.408 0.666755 17.1296L1.29175 13.5806C-1.27767 9.33574 0.111206 3.76869 4.34727 1.26352C8.58334 -1.24165 14.1388 0.080521 16.7083 4.32539C19.2777 8.57027 17.8888 14.1373 13.6527 16.7121C10.875 18.3822 7.33335 18.4518 4.5556 16.7817L1.70841 17.9647C1.36119 18.0343 1.15286 17.9647 0.94453 17.8255ZM4.5556 15.3203C4.69449 15.3203 4.83338 15.3899 4.90282 15.4595C8.51389 17.7559 13.2361 16.6425 15.4583 13.0935C17.6805 9.5445 16.6388 4.74292 13.0972 2.5161C9.55555 0.289285 4.83338 1.19393 2.54174 4.81251C0.94453 7.31768 0.94453 10.5883 2.54174 13.0935C2.61118 13.2327 2.68062 13.4414 2.61118 13.5806L2.19452 16.1554L4.20838 15.3203H4.5556Z" fill="currentColor"/>
			   </svg>
			   <?php esc_html_e( 'Listing FAQ', 'rtcl-faq' ); ?>
		   </p>
		</h3>
	</div>
	<div class="rtcl-faq-wrapper">
		<div id="rtcl-faq-items">
			<?php
			if ( $faqs ) :
				foreach ( $faqs as $index => $faq ) :
					?>
					<div class="faq-item">
						<textarea class="faq-title-input" name="rtcl_faq_title[]" rows="4" placeholder="<?php esc_attr_e( 'Title', 'rtcl-faq' ); ?>"><?php echo esc_textarea( $faq['title'] ); ?></textarea>
						<textarea class="faq-content-input" name="rtcl_faq_content[]" rows="4"
								  placeholder="<?php esc_attr_e( 'Content', 'rtcl-faq' ); ?>"><?php echo esc_textarea( $faq['content'] ); ?></textarea>
						<input type="hidden" class="faq-item-index" name="rtcl_faq_index[]"
							   value="<?php echo esc_attr( $index ); ?>">
						<button class="rtcl-remove-faq"><?php esc_html_e( 'Remove', 'rtcl-faq' ); ?></button>
						<span class="rtcl-faq-move">☰</span>
					</div>
					<?php
				endforeach;
			else :
				?>
				<div class="faq-item">
					<textarea class="faq-title-input" name="rtcl_faq_title[]" rows="4" placeholder="<?php esc_attr_e( 'FAQ Title', 'rtcl-faq' ); ?>"></textarea>
					<textarea class="faq-content-input" name="rtcl_faq_content[]" rows="4"
							  placeholder="<?php esc_attr_e( 'FAQ Content', 'rtcl-faq' ); ?>"></textarea>
					<input type="hidden" class="faq-item-index" name="rtcl_faq_index[]" value="0">
					<button class="rtcl-remove-faq"><?php esc_html_e( 'Remove', 'rtcl-faq' ); ?></button>
					<span class="rtcl-faq-move">☰</span>
				</div>
				<?php
			endif;
			?>
		</div>

		<div class="faq-bottom-wrapper">
			<button id="add-rtcl-faq" class="add-faq-button button"><?php esc_html_e( 'Add New FAQ', 'rtcl-faq' ); ?></button>
		</div>
	</div>

</div>