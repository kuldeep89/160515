<?php

	/**
	* PayProMedia Stock Widget
	* Author: Enrique Marrufo
	* Date: 17 August 2013
	*
	* Notes: This widget queries http://finance.google.com/finance/info for selected
	*		 stocks and returns a json string. PHP is used to parse the json string on
	*		 the initial load then ajax queries http://finance.google.com/finance/info
	*		 every seven seconds to update the stoc price, change, and change percent.
	*		 The updated data is injected by traversing the DOM using jQuery.
	*
	**/

	// Get stock quotes
	$stock_quotes = $this->widgets_model->get_quotes($widget_items);
?>
<div class="portlet dragme" column="<?php echo $widget_location['column'] ?>" row="<?php echo $widget_location['row'] ?>" widget_type="2" db_id="<?php echo $db_id ?>">
	<div class="portlet-title "style="padding:0px; margin-bottom: -5px; ">
		<div class="top-news">
			<a href="" class="btn green" style="margin-bottom: -10px;">
				<span>My Stocks</span>
				<em class="stock-time"><?php echo closed($stock_quotes[0]['lt']); ?></em>
				<i class="icon-money top-news-icon"></i>
			</a>
		</div>
		<div class="tools" style="margin-top: -1.8em;">
			<a href="javascript:;" class="collapse btn-group"></a>
		</div>
	</div>
		<div class="portlet-body no-more-tables stock">
			<table class=" news-blocks table-bordered table-striped table-condensed cf">
				<thead class="cf" style="text-align: left;">
					<tr>
						<th>Ticker</th>
						<th class="numeric">Price</th>
						<th class="numeric">Change</th>
						<th class="numeric">Change %</th>
					</tr>
				</thead>
				<tbody>
					
					<?php
						// Loop through and display stock quotes
						foreach ($stock_quotes as $cur_stock_quote) :
					?>
						<tr>
							<td data-title="Code" class="stock-symbol">
								<?php echo "\n".$cur_stock_quote['t']; ?>
							</td>
							<td data-title="Price" class="price numeric">
								<?php echo "\n".$cur_stock_quote['l']; ?>
							</td>
							<td data-title="Change" class="change numeric <?php echo change($cur_stock_quote['c']); ?>">
								<?php echo "\n".$cur_stock_quote['c']; ?> <span><?php echo arrow($cur_stock_quote['c']); ?></span>
							</td>
							<td data-title="Change %" class="percent-change numeric <?php echo change($cur_stock_quote['c']); ?>">
								<?php echo "\n".$cur_stock_quote['cp']."%" ?> <span><?php echo arrow($cur_stock_quote['cp']); ?></span>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
</div>
