<div class="box">
	<div class="box-heading"><?php echo $heading_title; ?></div>
	<div class="box-content">
	<div class="box-manufacturer" style="overflow: hidden;">
		<?php foreach ( $manufacturers as $manufacturer ) { ?>
			<div style="float: left; border: 1px solid #E7E7E7; padding: 3px; margin: 5px;">
				<?php if ( $manufacturer['image'] ) { ?>
					<div class="image">
						<?php
							$img = "<img src=\"{$manufacturer['image']}\" alt=\"{$manufacturer['name']}\" />";
							if ( $featured_manufacturer_has_link == 1 ) {
								$img = "<a href=\"{$manufacturer['href']}\">" . $img . "</a>";
							}
							echo $img;
						?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	</div>
</div>
