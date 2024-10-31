<?php

class TelegramPostNotification {
	/**
	 * @param string $err
	 *
	 * @return void
	 */
	static function sendError( string $err ) {
		add_action( 'admin_notices', function () use ( $err ) {
			?>
            <div class="notice notice-error is-dismissible">
                <p>Telegram Post Error: <? echo esc_html($err) ?></p>
            </div>
			<?php
		} );
	}
}