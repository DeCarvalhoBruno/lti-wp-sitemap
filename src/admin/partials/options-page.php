<?php

class htmlSelect {

	public $html;
	public $id;

	public function __construct( Array $elements, $name, $selectSelected = '', $selectID = '', $selectClass = '' ) {
		if ( ! empty( $selectID ) ) {
			$selectID = sprintf( 'id="%s"', $selectID );
		}
		if ( ! empty( $selectClass ) ) {
			$selectClass = sprintf( 'class="%s"', $selectClass );
		}
		$this->html = sprintf( '<select name="%s" %s %s>', $name, $selectID, $selectClass );
		foreach ( $elements as $value => $displayValue ) {
			$selected = '';
			if ( $value == $selectSelected ) {
				$selected = 'selected="selected"';
			}
			$this->html .= sprintf( '<option value="%s" %s>%s</option>', $value, $selected, $displayValue );
		}
		$this->html .= "</select>";
	}
}
//echo "<pre>";
//print_r($this->settings);
//echo "</pre>";

$changeFrequencies = array(
	"always"  => lsmint( 'opt.change_frequency.always' ),
	"hourly"  => lsmint( 'opt.change_frequency.hourly' ),
	"daily"   => lsmint( 'opt.change_frequency.daily' ),
	"weekly"  => lsmint( 'opt.change_frequency.weekly' ),
	"monthly" => lsmint( 'opt.change_frequency.monthly' ),
	"yearly"  => lsmint( 'opt.change_frequency.yearly' ),
	"never"   => lsmint( 'opt.change_frequency.never' )
);

$priorities                 = array(
	"1"   => "1",
	"0.9" => "0.9",
	"0.8" => "0.8",
	"0.7" => "0.7",
	"0.6" => "0.6",
	"0.5" => "0.5",
	"0.4" => "0.4",
	"0.3" => "0.3",
	"0.2" => "0.2",
	"0.1" => "0.1",
);
$changeFrequencyFrontpage   = new htmlSelect( $changeFrequencies, 'change_frequency_frontpage',
	lsmopt( 'change_frequency_frontpage' ), 'change_frequency_frontpage' );
$changeFrequencyPosts       = new htmlSelect( $changeFrequencies, 'change_frequency_posts',
	lsmopt( 'change_frequency_posts' ), 'change_frequency_posts' );
$changeFrequencyPages       = new htmlSelect( $changeFrequencies, 'change_frequency_pages',
	lsmopt( 'change_frequency_pages' ), 'change_frequency_pages' );
$changeFrequencyAuthors     = new htmlSelect( $changeFrequencies, 'change_frequency_authors',
	lsmopt( 'change_frequency_authors' ), 'change_frequency_authors' );
$changeFrequencyUserDefined = new htmlSelect( $changeFrequencies, 'change_frequency_user_defined',
	lsmopt( 'change_frequency_user_defined' ), 'change_frequency_user_defined' );
$priorityFrontpage          = new htmlSelect( $priorities, 'priority_frontpage', lsmopt( 'priority_frontpage' ),
	'priority_frontpage' );
$priorityPosts              = new htmlSelect( $priorities, 'priority_posts', lsmopt( 'priority_posts' ),
	'priority_posts' );
$priorityPages              = new htmlSelect( $priorities, 'priority_pages', lsmopt( 'priority_pages' ),
	'priority_pages' );
$priorityAuthors            = new htmlSelect( $priorities, 'priority_authors', lsmopt( 'priority_authors' ),
	'priority_authors' );
$priorityUserDefined        = new htmlSelect( $priorities, 'priority_user_defined', lsmopt( 'priority_user_defined' ),
	'priority_user_defined' );

$extraPages = "";
$extra_urls = $this->settings->get( "extra_pages_url" );
if ( ! empty( $extra_urls ) ) {
	$extra_dates = $this->settings->get( "extra_pages_date" );
	foreach ( $extra_urls as $key => $page ) {
		$extraPages .= sprintf( '
			<tr>
				<td>
					<input type="text" name="extra_pages_url[]" value="%s"/>
				</td>
				<td>
					<input type="text" name="extra_pages_date[]" value="%s"/>
				</td>
				<td>
					<button type="button" class="btn-del-row dashicons dashicons-no"></button>
				</td>
			</tr>',
			$page, $extra_dates[ $key ], $key
		);
	}
}

/**
 * LTI Sitemap plugin
 *
 * Admin View
 *
 * @see \Lti\Sitemap\Admin::options_page
 *
 */
?>
<div id="lti_sitemap_wrapper">

	<div id="lti-sitemap-header" class="lti-sitemap-header <?php echo lsmpagetype() ?>">
		<h2 class="lti-sitemap-title"><?php echo lsmint( 'opt.title' ); ?></h2>

		<h2 class="lti-sitemap-message"><?php echo lsmessage(); ?></h2>
	</div>
	<div role="tabpanel">
		<ul id="lti_sitemap_tabs" class="nav nav-tabs" role="tablist">
			<li role="presentation">
				<a href="#tab_general" aria-controls="tab_general" role="tab"
				   data-toggle="tab"><?php echo lsmint( 'opt.tab.general' ); ?></a>
			</li>
			<li role="presentation">
				<a href="#tab_google" aria-controls="tab_google" role="tab"
				   data-toggle="tab"><?php echo lsmint( 'opt.tab.google' ); ?></a>
			</li>
		</ul>

		<form id="flsm" accept-charset="utf-8" method="POST"
		      action="<?php echo admin_url( 'options-general.php?page=lti-sitemap-options' ); ?>">
			<?php echo wp_nonce_field( 'lti_sitemap_options', 'lti_sitemap_token' ); ?>
			<div class="tab-content">
				<?php
				/***********************************************************************************************
				 *                                  GENERAL TAB
				 ***********************************************************************************************/
				?>
				<div role="tabpanel" class="tab-pane active" id="tab_general">
					<div class="form-group">
						<div class="input-group">
							<div class="checkbox">
								<label><?php echo lsmint( 'opt.group.content' ); ?></label>

								<div class="checkbox-group">
									<label for="content_frontpage"><?php echo lsmint( 'opt.content_frontpage' ); ?>
										<input type="checkbox" name="content_frontpage"
										       id="content_frontpage" <?php echo lsmchk( 'content_frontpage' ); ?>/>
									</label>
									<label for="content_posts"><?php echo lsmint( 'opt.content_posts' ); ?>
										<input type="checkbox" name="content_posts"
										       id="content_posts" <?php echo lsmchk( 'content_posts' ); ?>
										       data-toggle="sitemap-options"
										       data-target="#content_posts_group"/>
									</label>
										<div id="content_posts_group">
											<div class="input-group">
												<label>
													<input name="content_posts_display"
													       type="radio" <?php echo lsmrad( 'content_posts_display',
														'normal' ); ?>
													       value="normal"
													       id="content_posts_normal"
														/><?php echo lsmint( 'opt.content_posts_normal' ); ?>
												</label>
												<label>
													<input name="content_posts_display"
													       type="radio" <?php echo lsmrad( 'content_posts_display',
														'year' ); ?>
													       value="year"
													       id="content_posts_year"
														/><?php echo lsmint( 'opt.content_posts_year' ); ?>
												</label>
												<label>
													<input name="content_posts_display"
													       type="radio" <?php echo lsmrad( 'content_posts_display',
														'month' ); ?>
													       value="month"
													       id="content_posts_month"
														/><?php echo lsmint( 'opt.content_posts_month' ); ?>
												</label>
											</div>
										</div>

									<label for="content_pages"><?php echo lsmint( 'opt.content_pages' ); ?>
										<input type="checkbox" name="content_pages"
										       id="content_pages" <?php echo lsmchk( 'content_pages' ); ?>/>
									</label>
									<label for="content_authors"><?php echo lsmint( 'opt.content_authors' ); ?>
										<input type="checkbox" name="content_authors"
										       id="content_authors" <?php echo lsmchk( 'content_authors' ); ?>/>
									</label>
									<label
										for="content_user_defined"><?php echo lsmint( 'opt.content_user_defined' ); ?>
										<input type="checkbox" name="content_user_defined"
										       id="content_user_defined" <?php echo lsmchk( 'content_user_defined' ); ?>/>
									</label>
								</div>
							</div>
						</div>
						<div class="form-help-container">
							<div class="form-help">
								<p><?php echo lsmint( 'opt.hlp.content' ); ?></p>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label><?php echo lsmint( 'opt.content_images' ); ?>
								<input type="checkbox" name="content_images" data-toggle="sitemap-options"
								       data-target="#images_chk_group"
								       id="content_images" <?php echo lsmchk( 'content_images' ); ?>/>
							</label>

							<div id="images_chk_group">
								<div class="checkbox-group">
									<label for="content_images_attachments"><?php echo lsmint( 'opt.content_images_attachments' ); ?>
										<input type="checkbox" name="content_images_attachments"
										       id="content_images_attachments" <?php echo lsmchk( 'content_images_attachments' ); ?>/>
									</label>
									<label for="content_images_featured"><?php echo lsmint( 'opt.content_images_featured' ); ?>
										<input type="checkbox" name="content_images_featured"
										       id="content_images_featured" <?php echo lsmchk( 'content_images_featured' ); ?>/>
									</label>
								</div>
							</div>
						</div>
						<div class="form-help-container">
							<div class="form-help">
								<p><?php echo lsmint( 'opt.hlp.images' ); ?></p>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label><?php echo lsmint( 'opt.group.change_frequency' ); ?></label>

							<div class="checkbox-group">
								<label
									for="change_frequency_frontpage"><?php echo lsmint( 'opt.change_frequency_frontpage' ); ?>
								</label>
								<?php echo $changeFrequencyFrontpage->html; ?>
								<label
									for="change_frequency_posts"><?php echo lsmint( 'opt.change_frequency_posts' ); ?>
								</label>
								<?php echo $changeFrequencyPosts->html; ?>
								<label
									for="change_frequency_pages"><?php echo lsmint( 'opt.change_frequency_pages' ); ?>
								</label>
								<?php echo $changeFrequencyPages->html; ?>
								<label
									for="change_frequency_authors"><?php echo lsmint( 'opt.change_frequency_authors' ); ?>
								</label>
								<?php echo $changeFrequencyAuthors->html; ?>
								<label
									for="change_frequency_user_defined"><?php echo lsmint( 'opt.change_frequency_user_defined' ); ?>
								</label>
								<?php echo $changeFrequencyUserDefined->html; ?>
							</div>
						</div>
						<div class="form-help-container">
							<div class="form-help">
								<p><?php echo lsmint( 'opt.hlp.change_frequency' ); ?></p>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label><?php echo lsmint( 'opt.group.priorities' ); ?></label>

							<div class="checkbox-group">
								<label for="priority_frontpage"><?php echo lsmint( 'opt.priority_frontpage' ); ?>
								</label>
								<?php echo $priorityFrontpage->html; ?>
								<label for="priority_posts"><?php echo lsmint( 'opt.priority_posts' ); ?>
								</label>
								<?php echo $priorityPosts->html; ?>
								<label for="priority_pages"><?php echo lsmint( 'opt.priority_pages' ); ?>
								</label>
								<?php echo $priorityPages->html; ?>
								<label for="priority_authors"><?php echo lsmint( 'opt.priority_authors' ); ?>
								</label>
								<?php echo $priorityAuthors->html; ?>
								<label for="priority_user_defined"><?php echo lsmint( 'opt.priority_user_defined' ); ?>
								</label>
								<?php echo $priorityUserDefined->html; ?>
							</div>
						</div>
						<div class="form-help-container">
							<div class="form-help">
								<p><?php echo lsmint( 'opt.hlp.priority' ); ?></p>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label><?php echo lsmint( 'opt.group.extra_pages' ); ?></label>

							<div class="checkbox-group">
								<div class="input-group">
									<table class="table">
										<thead>
										<tr>
											<th width="70%"><?php echo lsmint( 'opt.extra_pages.url' ); ?></th>
											<th width="27%"><?php echo lsmint( 'opt.extra_pages.date' ); ?></th>
											<th width="3%">
												<button type="button" class="dashicons dashicons-no"></button>
											</th>
										</tr>
										</thead>
										<tbody>
										<?php echo $extraPages ?>
										<tr>
											<td colspan="3">
												<button type="button" class="dashicons dashicons-plus"
												        id="btn_extra_pages_add"></button>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="form-help-container">
							<div class="form-help">
								<p><?php echo lsmint( 'opt.hlp.extra_pages' ); ?></p>
							</div>
						</div>
					</div>
				</div>
				<?php
				/***********************************************************************************************
				 *                                  GOOGLE TAB
				 ***********************************************************************************************/
				?>
				<div role="tabpanel" class="tab-pane" id="tab_google">
					<div class="form-group">
						<div class="input-group">
							<div class="checkbox">
								<label for="_support"></label>

								<div id="_chk_group">
									<div class="checkbox-group">

									</div>
								</div>
							</div>
						</div>
						<div class="form-help-container">
							<div class="form-help">
								<p></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group-submit">
				<div class="button-group-submit">
					<input id="in-seopt-submit" class="button-primary" type="submit" name="lti_sitemap_update"
					       value="<?php echo lsmint( 'general.save_changes' ); ?>"/>
					<input id="in-seopt-reset" class="button-primary" type="submit" name="lti_sitemap_reset"
					       value="<?php echo lsmint( 'general.reset_defaults' ); ?>"/>
				</div>
			</div>
		</form>
	</div>
</div>