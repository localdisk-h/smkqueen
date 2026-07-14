<?php
/**
 * Comments template.
 *
 * @package Queen_AlFalah
 */

if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments-area" aria-label="<?php esc_attr_e( 'Diskusi', 'queen-alfalah' ); ?>">
	<?php if ( have_comments() ) : ?>
		<h2 id="comments-title" class="comments-title">
			<?php
			$comment_count = get_comments_number();
			echo esc_html(
				sprintf(
					/* translators: %s: number of comments. */
					_n( '%s tanggapan', '%s tanggapan', $comment_count, 'queen-alfalah' ),
					number_format_i18n( $comment_count )
				)
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'screen_reader_text' => __( 'Navigasi tanggapan', 'queen-alfalah' ),
				'prev_text'          => __( 'Tanggapan sebelumnya', 'queen-alfalah' ),
				'next_text'          => __( 'Tanggapan berikutnya', 'queen-alfalah' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Tanggapan untuk konten ini telah ditutup.', 'queen-alfalah' ); ?></p>
	<?php endif; ?>

	<?php if ( comments_open() ) : ?>
		<?php
		comment_form(
			array(
				'title_reply'          => __( 'Tinggalkan tanggapan', 'queen-alfalah' ),
				'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title">',
				'title_reply_after'    => '</h2>',
				'label_submit'         => __( 'Kirim tanggapan', 'queen-alfalah' ),
				'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'Alamat email tidak akan dipublikasikan. Hindari menuliskan data pribadi atau informasi siswa.', 'queen-alfalah' ) . '</p>',
			)
		);
		?>
	<?php endif; ?>
</section>
