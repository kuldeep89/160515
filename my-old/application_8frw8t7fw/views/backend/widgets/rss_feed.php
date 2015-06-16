<?php
	$rss_feed_content = utf8_encode(file_get_contents($widget_items['rss_feed_url']));
	$xml = simplexml_load_string($rss_feed_content);
?>
<div class="portlet dragme" column="<?php echo $widget_location['column'] ?>" row="<?php echo $widget_location['row'] ?>" widget_type="5" db_id="<?php echo $db_id ?>">
	<div class="portlet-title">
		<div class="top-news">
			<a href="" class="btn green" style="margin-bottom: -10px;">
				<span>RSS Feed</span>
				<em><?php echo date('M j, h:iA T') ?></em>
				<i class="icon-rss top-news-icon"></i>
			</a>
		</div>
	
		<div class="tools" style="margin-top: -2.0em;">
			<a href="javascript:;" class="collapse btn-group"></a>
		</div>
	</div>
	<!-- End of Category Title -->
	
	<div class="portlet-body">
		<?php
			// Echo RSS feed items
			$i=0;
			foreach ($xml->channel->item as $cur_feed_item) :
				if ($i == $widget_items['rss_feed_num_articles']) :
					break;
				endif;
		?>
			<div class="news-blocks">
	            <h3><a href="<?php echo $cur_feed_item->link ?>" target="_blank"><?php echo $cur_feed_item->title ?></a></h3>
	            <div class="news-block-tags">
	                <em><?php echo $cur_feed_item->pubDate ?>.</em>
	            </div>
	            <p><?php echo $cur_feed_item->description ?></p>
				<a href="<?php echo $cur_feed_item->link ?>" target="_blank" class="news-block-btn">Read more <i class="m-icon-swapright m-icon-black"></i></a>
			</div>
		<?php
				$i++;
			endforeach;
		?>
	</div>
</div>