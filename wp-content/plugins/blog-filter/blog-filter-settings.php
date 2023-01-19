<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// toggle button CSS.
wp_enqueue_style( 'awl-blog-filter-settings-css', plugin_dir_url( __FILE__ ) . 'css/blog-filter-settings.css' );
wp_enqueue_style( 'awl-font-awesome-4-min-css', plugin_dir_url( __FILE__ ) . 'css/font-awesome-4.min.css' );
wp_enqueue_style( 'awl-bootstrap-css', plugin_dir_url( __FILE__ ) . 'css/blog-filter-bootstrap.css' );
wp_enqueue_style( 'awl-styles-css', plugin_dir_url( __FILE__ ) . 'css/styles.css' );
wp_enqueue_style( 'wp-color-picker' );
// js
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'wp-color-picker' );
wp_enqueue_script( 'awl-blog-filter-isotope-js', plugin_dir_url( __FILE__ ) . 'js/isotope.pkgd.js', array( 'jquery' ), '', false );
wp_enqueue_script( 'awl-bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), '', true ); ?>
<div class="panel panel-info" style="margin-top:20px; margin-bottom:10px;">
	<div class="panel-heading text-center">
		<h3 class="panel-title"><?php esc_html_e( 'Blog Filter Settings Page', 'blog-filter' ); ?></h3>
	</div>
	<div class="panel-body " style="padding-top:20px" id="BlogFilter-SettingsPags">
		<div class="module-wrapper masonry-item col-lg-4 col-md-4 col-sm-12 col-xs-12" style="">
			<section class="module module-headings">
				<div class="module-inner">
					<div class="module-heading">
						<h3 class="module-title"><?php esc_html_e( 'Column Settings', 'blog-filter' ); ?></h3>
					</div>
					<div class="module-content collapse in" id="content-1">
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Template Right To Left ', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<label class="switch">
									<input type="checkbox" id="blog_direction" name="blog_direction" value="rtl" >
									<div class="slider round"></div>
								</label>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Column On Large Desktop', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Column On Desktop', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Column On Tablet', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Column On Phone', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="module-wrapper masonry-item col-lg-4 col-md-4 col-sm-12 col-xs-12" style="">
			<section class="module module-headings">
				<div class="module-inner">
					<div class="module-heading">
						<h3 class="module-title"><?php esc_html_e( 'Image Settings', 'blog-filter' ); ?></h3>
					</div>
					<div class="module-content collapse in" id="content-1">
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Blog Images', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<label class="switch">
									<input type="checkbox" id="blog_image" name="blog_image" value="yes" checked >
									<div class="slider round"></div>
								</label>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Image Quality', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<select id="blog_image_quality" name="blog_image_quality" class="blog_image_quality" style="cursor:pointer;">
									<option value="thumbnail" ><?php esc_html_e( 'Thumbnail', 'blog-filter' ); ?></option>
									<option value="medium" selected><?php esc_html_e( 'Medium', 'blog-filter' ); ?></option>
									<option value="large" ><?php esc_html_e( 'Large', 'blog-filter' ); ?></option>
									<option value="full" ><?php esc_html_e( 'Full', 'blog-filter' ); ?></option>
								</select>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Image Hover Effect', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<select id="blog_image_hover_effect" name="blog_image_hover_effect" class="selectbox_position_newslide" style="cursor:pointer;">
									<option value="none" ><?php esc_html_e( 'None', 'blog-filter' ); ?> &nbsp &nbsp </option>
									<option value="hover1" selected ><?php esc_html_e( 'Hover 1', 'blog-filter' ); ?></option>
								</select>
							</div>
						</div>
						<p><?php esc_html_e( 'Get more hover effect in Pro', 'blog-filter' ); ?></p>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Link On Image', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Lightbox On Image', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="module-wrapper masonry-item col-lg-4 col-md-4 col-sm-12 col-xs-12" style="">
			<section class="module module-headings">
				<div class="module-inner">
					<div class="module-heading">
						<h3 class="module-title"><?php esc_html_e( 'Title Settings', 'blog-filter' ); ?></h3>
					</div>
					<div class="module-content collapse in" id="content-1">
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Blog Title', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<label class="switch">
									<input type="checkbox" id="blog_title" name="blog_title" value="yes" checked >
									<div class="slider round"></div>
								</label>
							</div>
						</div>
						<div class="module-content-inner title_setings">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Title Text Color', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<input type="text" class="form-control" id="blog_title_color" name="blog_title_color" value="#000" default-color="#000">
							</div>
						</div>
						<div class="module-content-inner title_setings">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Title Font Size', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner title_setings">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Title Below The Image', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner title_setings">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Link On Title', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="module-wrapper masonry-item col-lg-4 col-md-4 col-sm-12 col-xs-12" style="">
			<section class="module module-headings">
				<div class="module-inner">
					<div class="module-heading">
						<h3 class="module-title"><?php esc_html_e( 'Description Settings', 'blog-filter' ); ?></h3>
					</div>
					<div class="module-content collapse in" id="content-1">
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Blog Description', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<label class="switch">
									<input type="checkbox" id="blog_desc" name="blog_desc" value="yes" checked >
									<div class="slider round"></div>
								</label>
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'How Much Characters Show In Description', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<input type="number" id="blog_desc_words" name="blog_desc_words" value="100" style="width: -webkit-fill-available;" >
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Description Text Color', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Description Box Color', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Description Font Size', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="module-wrapper masonry-item col-lg-4 col-md-4 col-sm-12 col-xs-12" style="">
			<section class="module module-headings">
				<div class="module-inner">
					<div class="module-heading">
						<h3 class="module-title"><?php esc_html_e( 'Link (URL) Settings', 'blog-filter' ); ?></h3>
					</div>
					<div class="module-content collapse in" id="content-1">
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Read More Link', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<label class="switch">
									<input type="checkbox" id="blog_read_more" name="blog_read_more" value="yes" checked >
									<div class="slider round"></div>
								</label>
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Text For Read More Link', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
							<input type="text" id="blog_read_more_text" name="blog_read_more_text" value="Read More" style="width: -webkit-fill-available;" >
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Link Open In New Tab', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="module-wrapper masonry-item col-lg-4 col-md-4 col-sm-12 col-xs-12" style="">
			<section class="module module-headings">
				<div class="module-inner">
					<div class="module-heading">
						<h3 class="module-title"><?php esc_html_e( 'Post Meta Settings', 'blog-filter' ); ?></h3>
					</div>
					<div class="module-content collapse in" id="content-1">
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Post Date', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<label class="switch">
									<input type="checkbox" id="blog_date" name="blog_date" value="yes" checked >
									<div class="slider round"></div>
								</label>
							</div>
						</div>
						<div class="module-content-inner title_setings">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Date Below The Image', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Post Author', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Post Categories', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Post Tags', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="module-wrapper masonry-item col-lg-4 col-md-4 col-sm-12 col-xs-12" style="">
			<section class="module module-headings">
				<div class="module-inner">
					<div class="module-heading">
						<h3 class="module-title"><?php esc_html_e( 'Filter Settings', 'blog-filter' ); ?></h3>
					</div>
					<div class="module-content collapse in" id="content-1">
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Filters', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<label class="switch">
									<input type="checkbox" id="blog_filters" name="blog_filters" value="yes" checked >
									<div class="slider round"></div>
								</label>
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Show Filter "All"', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<label class="switch">
									<input type="checkbox" id="blog_filter_all" name="blog_filter_all" value="yes" >
									<div class="slider round"></div>
								</label>
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Text For "All" Button', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
							<input type="text" id="blog_all_text" name="blog_all_text" value="All" style="width: -webkit-fill-available;" >
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Buttons Color', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<input type="text" class="form-control" id="blog_buttons_color" name="blog_buttons_color" value="#58BBEE" default-color="#58BBEE">
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Already Selected First Filter', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
						<div class="module-content-inner">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Filtering with', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<select id="blog_filtering" name="blog_filtering" class="selectbox_position_newslide" style="cursor:pointer;">
									<option value="blog_category" selected><?php esc_html_e( 'Category', 'blog-filter' ); ?></option>
									<option value="blog_tag" ><?php esc_html_e( 'Tag', 'blog-filter' ); ?></option>
								</select>
							</div>
						</div>
						<div id="cat_filtering" class="module-content-inner ">
							<div class="table-responsive" style="max-height:400px;" >
								<?php
								$taxonomy_name = 'category';
								$term_args     = array( 'hide_empty' => true );
								$terms         = get_terms( $taxonomy_name, $term_args ); // Get all terms of a taxonomy. 
								if ( $terms && !is_wp_error( $terms ) ) : ?>
									<table class="table table-bordered">
										<thead>
											<tr>
												<th><?php esc_html_e( 'ID', 'blog-filter' ); ?></th>
												<th><?php esc_html_e( 'Category', 'blog-filter' ); ?></th>
												<th class="text-center"><?php esc_html_e( 'Select', 'blog-filter' ); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach ( $terms as $term ) { ?>
											<tr>
												<td><?php esc_html(_e($term->term_id)); ?></td>
												<td><?php esc_html(_e( $term->name, 'blog-filter' )); ?></td>
												<td class="text-center"><input class="checkbox_cat" type="checkbox" id="selected_categories[]" name="selected_categories[]" value="<?php echo esc_attr($term->term_id); ?>"></td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
									<?php
								endif; ?>
							</div>
							<p><b><?php esc_html_e( 'Note: In free version you can use only 4 categories as filters', 'blog-filter' ); ?></b></p>
						</div>
						<div id="tag_filtering" style="display:none;" class="module-content-inner ">
							<div class="table-responsive" style="max-height:400px;" >
								<?php
								$taxonomy_name = 'post_tag';
								$term_args     = array( 'hide_empty' => true );
								$terms         = get_terms( $taxonomy_name, $term_args ); // Get all terms of a taxonomy.
								if ( $terms && ! is_wp_error( $terms ) ) : ?>
									<table class="table table-bordered">
										<thead>
										  <tr>
											<th><?php esc_html_e( 'ID', 'blog-filter' ); ?></th>
											<th><?php esc_html_e( 'Post Tag', 'blog-filter' ); ?></th>
											<th class="text-center"><input class="checkbox_tag" type="checkbox" id="all_checked_tag" name="all_checked_tag"></th>
										  </tr>
										</thead>
										<tbody>
										<?php foreach ( $terms as $term ) { ?>
											<tr>
												<td><?php esc_html(_e($term->term_id)); ?></td>
												<td><?php esc_html(_e( $term->name, 'blog-filter' )); ?></td>
												<td class="text-center"><input class="checkbox_tag" type="checkbox" id="selected_tags[]" name="selected_tags[]" value="<?php echo esc_attr( $term->term_id); ?>"></td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
									<?php
								endif; ?>
							</div>
							<p><b><?php esc_html_e( 'Note: In free version you can use only 4 tags as filters', 'blog-filter' ); ?></b></p>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="module-wrapper masonry-item col-lg-4 col-md-4 col-sm-12 col-xs-12" style="">
			<section class="module module-headings">
				<div class="module-inner">
					<div class="module-heading">
						<h3 class="module-title"><?php esc_html_e( 'Pagination Settings', 'blog-filter' ); ?></h3>
					</div>
					<div class="module-content collapse in" id="content-1">
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Pagination', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<label class="switch">
									<input type="checkbox" id="blog_pagination" name="blog_pagination" value="yes" checked>
									<div class="slider round"></div>
								</label>
							</div>
						</div>
						<div class="module-content-inner title_setings">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Blogs On Per Page', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn btn-link" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Upgrade To Pro</a></p>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="module-wrapper masonry-item col-lg-4 col-md-4 col-sm-12 col-xs-12" style="">
			<section class="module module-headings">
				<div class="module-inner">
					<div class="module-heading">
						<h3 class="module-title"><?php esc_html_e( 'Try Pro Version 4.7 ', 'blog-filter' ); ?></h3>
					</div>
					<div class="module-content collapse in" id="content-1">
						<div class="module-content-inner title_setings">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Update Sortcode, Without Regenerate It', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn button_1 btn-primary" target="_blank" href="https://awplife.com/configure-blog-filter-plugin-shortcode/" style="text-decoration:none">See Post</a></p>
							</div>
						</div>
						<div class="module-content-inner title_setings">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Contact Us', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn button_1 btn-primary" target="_blank" href="https://awplife.com/contact" style="text-decoration:none">Contact</a></p>
							</div>
						</div>
						<div class="module-content-inner ">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'See Live Demo of Pro', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn button_1 btn-primary" target="_blank" href="https://awplife.com/demo/blog-filter-premium/" style="text-decoration:none">Live Demo</a></p>
							</div>
						</div>
						<div class="module-content-inner title_setings">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<p><b><?php esc_html_e( 'Buy Pro', 'blog-filter' ); ?></b></p>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
								<p><a class="btn button_1 btn-primary" target="_blank" href="https://awplife.com/product/blog-filter-wordpress-plugin/" style="text-decoration:none">Buy Pro Plugin</a></p>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
<div class="panel panel-info bf_pannel_bottom">
	<div class="panel-body eva-bottom-panel">
		<div class="col-md-6 text-left">
			<div class="eva_option_head">
			<h3 class="bf_footer_title"><?php esc_html_e( 'Blog Filter', 'blog-filter' ); ?> 
				<p style="display:inline;">
				<?php
					esc_html_e( 'Version - ', 'blog-filter' );
					esc_html_e( BF_PLUGIN_VER, 'blog-filter' );
				?>
				<p>
			</h3>
			</div>
		</div>
		<div class="col-md-6 text-right">
			<div class="eva_option_head">
			<button type="button" onclick="BfGetShortcode();" class="bf_button button_1"><?php esc_html_e( '[ Generate Sortcode ]', 'blog-filter' ); ?></button>
			</div>
		</div>
	</div>
</div>
<div class="loader" style="display:none;"></div>
<div class="modal" id="modal-show-shortcode" tabindex="-1" role="dialog" aria-labelledby="modal-new-short-code-label">
	<div class="modal-dialog" role="document" id="inner-modal">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modal-new-ticket-label"><?php esc_html_e( 'Blog Filter Shortcode', 'blog-filter' ); ?></h4>
			</div>
			<div id="" class="modal-body text-center">
				<p><?php esc_html_e( 'Copy The Shortcode', 'blog-filter' ); ?></p>
				<textarea id="awl-shortcode" readonly rows="13" cols="120" style="width: 468px;">
				</textarea>
				<div id="" class="center-block text-center">
					<button type="button" class="bf_button button_1" onclick="CopyShortcode()" ><i class="fa fa-copy" aria-hidden="true"></i> <?php esc_html_e( 'Copy Sortcode', 'blog-filter' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
//short code []
function BfGetShortcode() {
	var shortcode = '[AWL-BlogFilter';
	var blog_direction = jQuery("#blog_direction").val();
	if(jQuery("#blog_direction").prop('checked') == true){
		shortcode = shortcode + ' blog_direction="' + blog_direction + '"';
	} else {
		shortcode = shortcode + '';
	}
	
	var blog_image = jQuery("#blog_image").val();
	if(jQuery("#blog_image").prop('checked') == true){
		shortcode = shortcode + ' blog_image="' + blog_image + '"';
	} else {
		shortcode = shortcode + '';
	}
	
	var blog_image_quality = jQuery("#blog_image_quality").val();
	if(blog_image_quality){
		shortcode = shortcode + ' blog_image_quality="' + blog_image_quality + '"';
	}
	
	var blog_image_hover_effect = jQuery("#blog_image_hover_effect").val();
	if(blog_image_hover_effect){
		shortcode = shortcode + ' blog_image_hover_effect="' + blog_image_hover_effect + '"';
	}
	
	var blog_title = jQuery("#blog_title").val();
	if(jQuery("#blog_title").prop('checked') == true){
		shortcode = shortcode + ' blog_title="' + blog_title + '"';
	} else {
		shortcode = shortcode + '';
	}
	
	var blog_title_color = jQuery("#blog_title_color").val();
	if(blog_title_color){
		shortcode = shortcode + ' blog_title_color="' + blog_title_color + '"';
	}
	
	var blog_desc = jQuery("#blog_desc").val();
	if(jQuery("#blog_desc").prop('checked') == true){
		shortcode = shortcode + ' blog_desc="' + blog_desc + '"';
	} else {
		shortcode = shortcode + '';
	}
	
	var blog_desc_words = jQuery("#blog_desc_words").val();
	if(blog_desc_words){
		shortcode = shortcode + ' blog_desc_words="' + blog_desc_words + '"';
	}
	
	var blog_read_more = jQuery("#blog_read_more").val();
	if(jQuery("#blog_read_more").prop('checked') == true){
		shortcode = shortcode + ' blog_read_more="' + blog_read_more + '"';
	} else {
		shortcode = shortcode + '';
	}
	
	var blog_read_more_text = jQuery("#blog_read_more_text").val();
	if(blog_read_more_text){
		shortcode = shortcode + ' blog_read_more_text="' + blog_read_more_text + '"';
	}
	
	var blog_date = jQuery("#blog_date").val();
	if(jQuery("#blog_date").prop('checked') == true){
		shortcode = shortcode + ' blog_date="' + blog_date + '"';
	} else {
		shortcode = shortcode + '';
	}
	
	var blog_pagination = jQuery("#blog_pagination").val();
	if(jQuery("#blog_pagination").prop('checked') == true){
		shortcode = shortcode + ' blog_pagination="' + blog_pagination + '"';
	} else {
		shortcode = shortcode + '';
	}
	
	var blog_filters = jQuery("#blog_filters").val();
	if(jQuery("#blog_filters").prop('checked') == true){
		shortcode = shortcode + ' blog_filters="' + blog_filters + '"';
	} else {
		shortcode = shortcode + '';
	}
	
	var blog_filter_all = jQuery("#blog_filter_all").val();
	if(jQuery("#blog_filter_all").prop('checked') == true){
		shortcode = shortcode + ' blog_filter_all="' + blog_filter_all + '"';
	} else {
		shortcode = shortcode + '';
	}
	
	var blog_all_text = jQuery("#blog_all_text").val();
	if(blog_all_text){
		shortcode = shortcode + ' blog_all_text="' + blog_all_text + '"';
	}
	
	var blog_buttons_color = jQuery("#blog_buttons_color").val();
	if(blog_buttons_color){
		shortcode = shortcode + ' blog_buttons_color="' + blog_buttons_color + '"';
	}
	
	var blog_filtering = jQuery("#blog_filtering").val();
	if(blog_filtering){
		shortcode = shortcode + ' blog_filtering="' + blog_filtering + '"';
	}
	
	if( blog_filtering == 'blog_category' ) {
		var selected_categories = [];
		jQuery('.checkbox_cat:checked').map(function() {
			if (jQuery.isNumeric(this.value)) {
				selected_categories.push(this.value);
			}
		});
		shortcode = shortcode + ' selected_categories="' + selected_categories + '"';
	} else if( blog_filtering == 'blog_tag' ) {
		var selected_tags = [];
		jQuery('.checkbox_tag:checked').map(function() {
			if (jQuery.isNumeric(this.value)) {
				selected_tags.push(this.value);
			}
		});
		shortcode = shortcode + ' selected_tags="' + selected_tags + '"';
	}
	
	shortcode = shortcode + ' custom-css="' + ' "';
	
	shortcode = shortcode + ' ]';
	
	jQuery('#awl-shortcode').text(shortcode);
	jQuery('#modal-show-shortcode').modal('show');
}

function CopyShortcode() {
	var copyText = document.getElementById("awl-shortcode");
	copyText.select();
	document.execCommand("copy");
}

jQuery('.checkbox_cat').click(function () {
	jQuery(this).next().next().prop('disabled', !this.checked)
	jQuery('.checkbox_cat').not(':checked').prop('disabled', jQuery('.checkbox_cat:checked').length == 4);
});
jQuery('.checkbox_tag').click(function () {
	jQuery(this).next().next().prop('disabled', !this.checked)
	jQuery('.checkbox_tag').not(':checked').prop('disabled', jQuery('.checkbox_tag:checked').length == 4);
});

jQuery(document).ready(function () {
	// isotope effect function.
	// Method 1 - Initialize Isotope, then trigger layout after each image loads.
	var $grid = jQuery('#BlogFilter-SettingsPags').isotope({
		// options.
		itemSelector: '.module-wrapper',
	});
	
	//range slider.
	var rangeSlider = function(){
	  var slider = jQuery('.range-slider'),
		range = jQuery('.range-slider__range'),
		value = jQuery('.range-slider__value');
		
	  slider.each(function(){
		value.each(function(){
		  var value = jQuery(this).prev().attr('value');
		  jQuery(this).html(value);
		});
		range.on('input', function(){
		  jQuery(this).next(value).html(this.value);
		});
	  });
	};
	rangeSlider();
	
	//color-picker.
	(function( jQuery ) {
		jQuery(function() {
			// Add Color Picker to all inputs that have 'color-field' class.
			jQuery('#blog_title_color, #blog_desc_color, #blog_desc_box_color, #blog_buttons_color').wpColorPicker();
		});
	})( jQuery );
	jQuery(document).ajaxComplete(function() {
		jQuery('#blog_title_color, #blog_desc_color, #blog_desc_box_color, #blog_buttons_color').wpColorPicker();
	});
	jQuery('#blog_filtering').change(function () {
		var blog_filtering = jQuery('#blog_filtering').val();
		if (blog_filtering == 'blog_category') {
			jQuery('#cat_filtering').show();
			jQuery('#tag_filtering').hide();
		} else if (blog_filtering == 'blog_tag') {
			jQuery('#cat_filtering').hide();
			jQuery('#tag_filtering').show();
		}
	});
});
</script>
