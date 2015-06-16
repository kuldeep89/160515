<?php get_header(); ?>

<div class="row full-width">
	<div class="small-12 large-8 columns" role="main">
	<?php do_action('foundationPress_before_content'); ?>
	
	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<header>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php // FoundationPress_entry_meta(); ?>
			</header>
			<?php do_action('foundationPress_post_before_entry_content'); ?>
					<?php
					$feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
					?>
			<div class="entry-content">
				<aside style="font-size:1rem !important;">Posted by <em style="font-size:1rem !important;"><?php the_author(); ?></em> on <em style="font-size:1rem !important;"><?php the_time('F jS, Y'); ?></em></aside>
			
				<?php if ( has_post_thumbnail() ): ?>
					<div class="header_image"><?php the_post_thumbnail(); ?></div>
				<?php endif; ?>
				<div class="share42init" data-url="<?php the_permalink() ?>" data-image="<?php echo $feat_image; ?>" data-title="<?php the_title() ?>"></div>
				<br />
				<?php the_content(); ?>
			</div>
			<footer>
				<?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . __('Pages:', 'FoundationPress'), 'after' => '</p></nav>' )); ?>
				<p>Categories: 
				<?php 
					$i=0;
					$numItems = count(get_the_category());
					foreach( (get_the_category()) as $category ) { 
						$i++;
					    echo '<a href="'.get_category_link($category->term_id ).'">'.$category->cat_name.'</a>';
					    if($i != $numItems){
						    echo ", ";
					    } 
					} 
				?>
				<br />
				<?php the_tags(); ?></p>
				<div id="author_info">
					<?php echo get_avatar(get_the_author_meta( 'ID' ), 150 ); ?>
					<h5><?php the_author_meta( 'display_name' ); ?></h5>
					<p><?php the_author_meta( 'user_description' ); ?> <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">More posts from <?php the_author_meta( 'display_name' ); ?></a></p>
				</div>
			</footer>
			
			<?php do_action('foundationPress_post_before_comments'); ?>
			<?php comments_template(); ?>
			<?php do_action('foundationPress_post_after_comments'); ?>
		</article>
	<?php endwhile;?>
	
	<?php do_action('foundationPress_after_content'); ?>

	</div>
	<?php get_sidebar('blog'); ?>
</div>
<?php get_footer(); ?>