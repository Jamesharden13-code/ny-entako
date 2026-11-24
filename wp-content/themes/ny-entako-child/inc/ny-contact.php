<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_shortcode( 'ny_contact_form', 'ny_render_contact_form_shortcode' );
function ny_render_contact_form_shortcode() {
	$status_message = '';

	if ( isset( $_GET['contact_sent'] ) ) {
		$status = sanitize_key( wp_unslash( $_GET['contact_sent'] ) );
		if ( 'success' === $status ) {
			$status_message = '<div class="ny-alert ny-alert-success" style="padding:14px 18px;margin-bottom:20px;border-radius:8px;background:#e6f4ea;color:#137333;">Votre message a bien été envoyé. Nous vous répondrons rapidement.</div>';
		} elseif ( 'error' === $status ) {
			$status_message = '<div class="ny-alert ny-alert-error" style="padding:14px 18px;margin-bottom:20px;border-radius:8px;background:#fce8e6;color:#c5221f;">Une erreur est survenue lors de l\'envoi. Veuillez vérifier vos champs et réessayer.</div>';
		}
	}

	ob_start();
	echo $status_message;
	?>
	<div class="ny-form-container">
		<?php echo $status_message; ?>
		<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ny-block-form">
			<!-- Hidden fields for admin-post.php and security -->
			<input type="hidden" name="action" value="ny_send_contact">
			<input type="hidden" name="ny_submit_contact" value="1">
			<?php wp_nonce_field( 'ny_contact_action', 'ny_contact_nonce' ); ?>

			<h3 class="ny-title-form">Envoyer nous un message</h3>

			<div class="ny-form-group">
				<label for="ny_name" class="ny-style-texte">Nom Complet</label>
				<input type="text" placeholder="rakoto" class="custom-input" name="ny_name" id="ny_name" required>
			</div>

			<div class="ny-form-group">
				<label for="ny_email" class="ny-style-texte">Adresse e-mail</label>
				<input type="email" placeholder="rakoto@gmail.com" class="custom-input" name="ny_email" id="ny_email" required>
			</div>

			<div class="ny-form-group">
				<label for="ny_subject" class="ny-style-texte">Sujet</label>
				<input type="text" placeholder="Sujet" class="custom-input" name="ny_subject" id="ny_subject" required>
			</div>

			<div class="ny-form-group">
				<label for="ny_message" class="ny-style-texte">Message</label>
				<textarea id="ny_message" name="ny_message" rows="4" cols="50" class="custom-textarea" placeholder="Partagez vos pensées ici" required></textarea>
			</div>

			<div class="ny-btn-wrapper">
				<button type="submit" class="ny-btn-contact" style="background: #412817; border: none; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; text-align: left; height: 80px">
					<span class="text-button">Envoyer le message</span>
					<i class="fa-solid fa-arrow-right icon-color" style="margin-left: 10px; color: white;"></i>
				</button>
			</div>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Secure processing of the NY ENTAKO contact form via admin-post.php
 */
add_action( 'admin_post_ny_send_contact', 'ny_handle_contact_form_submission' );
add_action( 'admin_post_nopriv_ny_send_contact', 'ny_handle_contact_form_submission' );

function ny_handle_contact_form_submission() {
	if ( ! isset( $_POST['ny_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ny_contact_nonce'] ) ), 'ny_contact_action' ) ) {
		wp_die( 'Action non autorisée (erreur de sécurité).', 'Erreur', array( 'response' => 403 ) );
	}

	$redirect_url = wp_get_referer() ? wp_get_referer() : home_url();

	if ( isset( $_POST['ny_submit_contact'] ) && '1' === $_POST['ny_submit_contact'] ) {
		$name    = isset( $_POST['ny_name'] ) ? sanitize_text_field( wp_unslash( $_POST['ny_name'] ) ) : '';
		$email   = isset( $_POST['ny_email'] ) ? sanitize_email( wp_unslash( $_POST['ny_email'] ) ) : '';
		$subject = isset( $_POST['ny_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['ny_subject'] ) ) : '';
		$msg     = isset( $_POST['ny_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ny_message'] ) ) : '';

		if ( empty( $name ) || ! is_email( $email ) || empty( $msg ) ) {
			wp_safe_redirect( add_query_arg( 'contact_sent', 'error', $redirect_url ) );
			exit;
		}

		$to           = get_option( 'admin_email' );
		$full_subject = '[NY ENTAKO Contact] ' . $subject;

		$headers   = array();
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$headers[] = 'From: NY ENTAKO <' . $to . '>';
		$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';

		$body  = '<h2>Nouveau message depuis le site NY ENTAKO</h2>';
		$body .= '<p><strong>Nom : </strong>' . esc_html( $name ) . '</p>';
		$body .= '<p><strong>Email : </strong>' . esc_html( $email ) . '</p>';
		$body .= '<p><strong>Sujet : </strong>' . esc_html( $subject ) . '</p>';
		$body .= '<p><strong>Message : </strong><br>' . nl2br( esc_html( $msg ) ) . '</p>';

		$send = wp_mail( $to, $full_subject, $body, $headers );

		if ( $send ) {
			wp_safe_redirect( add_query_arg( 'contact_sent', 'success', $redirect_url ) );
			exit;
		} else {
			wp_safe_redirect( add_query_arg( 'contact_sent', 'error', $redirect_url ) );
			exit;
		}
	}

	wp_safe_redirect( home_url() );
	exit;
}