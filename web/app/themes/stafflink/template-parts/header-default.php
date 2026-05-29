<?php
/**
 * Default site header.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header id="sticky-header " class="header fixed-top">
	<nav class="navbar navbar-expand-md" aria-label="<?php esc_attr_e( 'Primary navigation', TEXT_DOMAIN ); ?>">
		<div class="container align-items-end">
			<?php if ( has_custom_logo() ) : ?>
				<div class="navbar-brand">
					<?php the_custom_logo(); ?>
				</div>
			<?php else : ?>
				<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>

			<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#btn-canvas-navbar" aria-controls="btn-canvas-navbar" aria-label="<?php esc_attr_e( 'Toggle navigation', TEXT_DOMAIN ); ?>">
				<span></span>
				<span></span>
				<span></span>
			</button>

			<div id="btn-canvas-navbar" class="offcanvas offcanvas-end" tabindex="-1" aria-labelledby="btn-canvas-navbar-label">
				<div class="offcanvas-header d-md-none">
					<h2 id="btn-canvas-navbar-label" class="visually-hidden"><?php esc_html_e( 'Main Menu', TEXT_DOMAIN ); ?></h2>
					<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php esc_attr_e( 'Close', TEXT_DOMAIN ); ?>"></button>
				</div>
				<div class="offcanvas-body justify-content-end">
					<?php
					if ( has_nav_menu( 'header-menu' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'header-menu',
								'container'      => false,
								'menu_class'     => 'navbar-nav align-items-md-center',
								'depth'          => 2,
								'walker'         => class_exists( 'WP_Bootstrap_Navwalker' ) ? new WP_Bootstrap_Navwalker() : '',
							)
						);
					} else {
						?>
						<ul class="navbar-nav align-items-md-center">
							<li class="nav-item"><a class="nav-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', TEXT_DOMAIN ); ?></a></li>
						</ul>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</nav>
</header>
