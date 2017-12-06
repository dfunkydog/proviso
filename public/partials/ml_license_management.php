<?php

/**
 * Provide a public-facing view for user license management page
 *
 * This file is used to markup the public-facing aspects of user license management page.
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
			<th class="license-order-number">
				<span class="nobr">Order</span>
			</th>
			<th class="license-course">
				<span class="nobr">Course</span>
			</th>
			<th class="license-asssignment">
				<span class="nobr">Assigned to</span>
			</th>
			<th class="license-status">
				<span class="nobr">Status</span>
			</th>
			<th class="license-actions">
				<span class="nobr">Actions</span>
			</th>
		  </tr>
	  </thead>

	  <tbody>
		<tr class="license">
		  <td class="license-order-number" data-title="license">101</td>
		  <td class="license-course" data-course="Date">Food hygene and safety</td>
		  <td class="license-assignment" data-title="Status">scott.eager@foo.bar</td>
		  <td class="license-status" data-title="Total">Unassigned</td>
		  <td class="license-actions" data-title="Actions"><a href="#" class="ml-button button -small">View course</a></td>
		</tr>
	  </tbody>
	</table>
  </div>

