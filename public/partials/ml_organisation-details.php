<?php

/**
 * HTML output for Organistaion Details shortcode
 *
 * This file is used to markup the public-facing aspects of organisation details shortcode.
 *
 * @link       nlsltd.com
 * @since      1.0.0
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/public/partials
 */
?>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php ob_start; ?>

<div class="organisation-details">
	<h2>Organisation details</h2>
	<p>You must provide your organisation sector and name before distributing licecces. <strong>Note: This cannot be changed once set</strong></p>
	<form class="organisation-details__form" data-form="organisation-details">
		<label class="organisation-details__sector" >Organisation sector<br>
			<div class="field-select -small">
				<select name="orderby" id="ml-orderby" class="orderby">
					<option value="popularity">Most Popular</option>
					<option value="date">Most Recent</option>
					<option value="price">Low to High</option>
					<option value="price-desc" selected="selected">High to Low</option>
					<option value="alphabet">A to Z</option>
					<option value="rev_alphabet">Z to A</option>
				</select>
			</div>
		</label>
		<label class="organisation-details__name">Organisation name<br>
			<input class="input-text" type="text" placeholder="" name="organisation_name">
		</label>
		<div class="organisation-details__controls">
			<button class="button" type="submit">Save</button>
		</div>
	</form>

</div>


  <?php
  return ob_get_clean;
