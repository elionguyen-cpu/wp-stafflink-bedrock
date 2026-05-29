<?php get_header(); ?>

<?php get_template_part( 'template-parts/banner' ); ?>

<?php get_template_part( 'template-parts/breadcrumbs' ); ?>
<?php get_template_part( 'template-parts/mailing' ); ?>

<?php while ( have_posts() ) : the_post(); ?>
	<div class="container">
		<?php the_content(); ?>
	</div>
<?php endwhile; ?>

<?php get_footer(); ?>
