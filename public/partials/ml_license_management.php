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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="woocommerce-MyAccount-content">
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
		<tr class="licence">
		  <td class="licence-order-number" data-title="licence">101</td>
		  <td class="licence-course" data-course="Date">Food hygene and safety</td>
		  <td class="licence-assignment" data-title="Status">scott.eager@foo.bar</td>
		  <td class="licence-status" data-title="Total">Unassigned</td>
		  <td class="licence-actions" data-title="Actions"><a href="#" class="ml-button button -small">View course</a></td>
		</tr>
	  </tbody>
	</table>
  </div>

