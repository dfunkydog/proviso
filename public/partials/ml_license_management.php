<?php

/**
 * Provide a public-facing view for user licence management page
 *
 * This file is used to markup the public-facing aspects of user licence management page.
 *
 * @link       nlsltd.com
 * @since      1.0.0
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/public/partials
 */
?>


<?php
			function assignment($val){
				switch ($val) {
					case '0':
						return 'Unassigned';
						break;
					case '10':
						return 'Unassigned';
						break;
					case '20':
						return 'Pending';
						break;
					case '30':
						return 'Redeemed';
						break;
					default:
						return 'Unassigned';
						break;
				}
			}
			?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="woocommerce-MyAccount-content">
	<h2>License management</h2>
	<?php do_shortcode('[ml_organisation_details]'); ?>

	<table class="woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
	  <thead>
		<tr>
			<th class="licence-order-number">
				<span class="nobr">Order</span>
			</th>
			<th class="licence-course">
				<span class="nobr">Course</span>
			</th>
			<th class="licence-asssignment">
				<span class="nobr">Assigned to</span>
			</th>
			<th class="licence-status">
				<span class="nobr">Status</span>
			</th>
			<th class="licence-actions">
				<span class="nobr">Actions</span>
			</th>
		  </tr>
	  </thead>

	  <tbody>
		<?php foreach($licenses as $license) :
			$assignment = assignment($license->status);
			$product = wc_get_product( wc_get_product_id_by_sku( $license->sku ) );?>
			<tr class="licence">
				<td class="licence-order-number" data-title="licence"><?php echo $license->ordernumber ?></td>
				<td class="licence-course" data-course="Date"><?php echo $product->get_name(); ?></td>
				<td class="licence-assignment" data-title="Status"><?php echo $license->sentfrom ?></td>
				<td class="licence-status" data-title="Total"><?php echo $assignment ;?></td>
				<td class="licence-actions" data-title="Actions"><?php
				 switch ($assignment) {
					 case 'Unassigned':
						 echo "<a href='#' class='button -small ml-button -lime'>Assign</a>";
						 break;
					 case 'Pending':
						 echo "<a href='#' class='button -small ml-button'>Cancel</a><a href='#' class='button -small ml-button'>Resend</a>";
						 break;
					 case 'Redeemed':
						 echo "<a href='#' class='button -small ml-button'>View Course</a>";
						 break;
					 case 'Assigned':
						 echo "<a href='#' class='button -small ml-button'>View Course</a>";
						 break;

					 default:
						 echo "";
						 break;
				 }
				 ?>
				 </td>
			</tr>
		<?php endforeach; ?>
	  </tbody>
	</table>
  </div>

