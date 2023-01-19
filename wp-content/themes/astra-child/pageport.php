<?php /* Template Name: pageportfolio*/ ?>

<form id="myform" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
  <input type="hidden" name="action" value="your_form_action">
  <input type="hidden" name="nonce_field_name" value="<?php echo wp_create_nonce( 'your_nonce_action' ); ?>">
  <label for="title">Title:</label>
  <input type="text" id="title" name="title"><br>
  <label for="description">Description:</label>
  <textarea id="description" name="description"></textarea><br>
  <input type="button" value="Submit" onclick="submitForm()">
</form>

<div id="response"></div>

