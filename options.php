<div class="wrap">
<h2>GrowSumo</h2>

<form method="post" action="options.php" style="margin-top:1rem;">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('growsumo'); ?>

<div class="">
    <span style="font-weight:bold;padding-right:0.5rem">GrowSumo API Key:</span>
<input style="width:310px" type="text" name="growsumo_company_key" value="<?php echo get_option('growsumo_company_key'); ?>" /></td>
</div>
<a href="https://docs.growsumo.com/docs/woocommerce" target="_blank">For information about how this plugin works visit our docs</a>
<?php submit_button(); ?>
</form>
</div>
