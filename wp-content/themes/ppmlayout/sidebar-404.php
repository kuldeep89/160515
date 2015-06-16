<section class="texts">
	<div class="container missing">
		<div class="row">
			<article class="col-xs-12">
				<header>
					<h1><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'ppmlayout' ); ?></h1>
				</header>

				<div>
					<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching or one of the links below, can help.', 'ppmlayout' ); ?></p>
					<div class="row">
						
						<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<div class="col-md-5 col-sm-8 col-xs-12">
								<input type="text" name="s" id="s" placeholder="<?php esc_attr_e( 'Search', 'ppmlayout' ); ?>" />
							</div>
							<div class="col-md-2 col-sm-4 col-xs-6">
								<input type="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'ppmlayout' ); ?>" />
							</div>
						</form>
						<div class="col-xs-12">
							<?php the_widget( 'WP_Widget_Recent_Posts', array( 'number' => 10 ), array( 'widget_id' => '404' ) ); ?>
						</div>
					</div>
				</div>
			</article>
		</div>
	</div>
</section>
