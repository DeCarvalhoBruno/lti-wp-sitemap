<?php
/**
 * LTI Sitemap plugin
 *
 * Admin View
 *
 * @see \Lti\Sitemap\Admin::options_page
 * @var $this \Lti\Sitemap\Admin
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
			<?php if ( $this->google->can_send_curl_requests ): ?>
				<li role="presentation">
					<a href="#tab_google" aria-controls="tab_google" role="tab"
					   data-toggle="tab"><?php echo lsmint( 'opt.tab.google' ); ?></a>
				</li>
				<li role="presentation">
					<a href="#tab_bing" aria-controls="tab_bing" role="tab"
					   data-toggle="tab"><?php echo lsmint( 'opt.tab.bing' ); ?></a>
				</li>
			<?php endif; ?>
		</ul>

		<form id="flsm" accept-charset="utf-8" method="POST"
		      action="<?php echo $this->get_admin_slug(); ?>">
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
							<label><?php echo lsmint( 'opt.content_news_support' ); ?>
								<input type="checkbox" name="content_news_support" data-toggle="sitemap-options"
								       data-target="#news_chk_group"
								       id="content_news_support" <?php echo lsmchk( 'content_news_support' ); ?>/>
							</label>

							<div id="news_chk_group">
								<div class="input-group">
									<label
										for="news_publication"><?php echo lsmint( 'opt.news_publication' ); ?>
										<input type="text" name="news_publication"
										       id="news_publication" required="required"
										       value="<?php echo lsmopt( 'news_publication' ); ?>"/>
									</label>
									<label
										for="news_language"><?php echo lsmint( 'opt.news_language' ); ?>
									</label>
										<?php $this->html->select('language','news_language');?>

									<label><?php echo lsmint( 'opt.news_access' ); ?></label>
									<div class="input-group">
										<label>
											<input name="news_access_type"
											       type="radio" <?php echo lsmrad( 'news_access_type',
												'Full' ); ?>
											       value="Full"
											       id="news_access_type_full"
												/><?php echo lsmint( 'opt.news_access_type_full' ); ?>
										</label>
										<label>
											<input name="news_access_type"
											       type="radio" <?php echo lsmrad( 'news_access_type',
												'Subscription' ); ?>
											       value="Subscription"
											       id="news_access_type_subscription"
												/><?php echo lsmint( 'opt.news_access_type_subscription' ); ?>
										</label>
										<label>
											<input name="news_access_type"
											       type="radio" <?php echo lsmrad( 'news_access_type',
												'Registration' ); ?>
											       value="Registration"
											       id="news_access_type_registration"
												/><?php echo lsmint( 'opt.news_access_type_registration' ); ?>
										</label>
									</div>
									<label><?php echo lsmint( 'opt.news_keywords' ); ?></label>
									<div class="input-group">
										<label><?php echo lsmint( 'opt.news_keywords_cat_based' ); ?>
											<input type="checkbox" name="news_keywords_cat_based"
											       id="news_keywords_cat_based" <?php echo lsmchk( 'news_keywords_cat_based' ); ?>/>
										</label>
										<label><?php echo lsmint( 'opt.news_keywords_tag_based' ); ?>
											<input type="checkbox" name="news_keywords_tag_based"
											       id="news_keywords_tag_based" <?php echo lsmchk( 'news_keywords_tag_based' ); ?>/>
										</label>
									</div>
									<label><?php echo lsmint( 'opt.news_genre' ); ?></label>
									<div class="input-group">
										<label><?php echo lsmint( 'opt.news_genre_press_release' ); ?>
											<input type="checkbox" name="news_genre_press_release"
											       id="news_genre_press_release" <?php echo lsmchk( 'news_genre_press_release' ); ?>/>
										</label>
										<label><?php echo lsmint( 'opt.news_genre_satire' ); ?>
											<input type="checkbox" name="news_genre_satire"
											       id="news_genre_satire" <?php echo lsmchk( 'news_genre_satire' ); ?>/>
										</label>
										<label><?php echo lsmint( 'opt.news_genre_blog' ); ?>
											<input type="checkbox" name="news_genre_blog"
											       id="news_genre_blog" <?php echo lsmchk( 'news_genre_blog' ); ?>/>
										</label>
										<label><?php echo lsmint( 'opt.news_genre_oped' ); ?>
											<input type="checkbox" name="news_genre_oped"
											       id="news_genre_oped" <?php echo lsmchk( 'news_genre_oped' ); ?>/>
										</label>
										<label><?php echo lsmint( 'opt.news_genre_opinion' ); ?>
											<input type="checkbox" name="news_genre_opinion"
											       id="news_genre_opinion" <?php echo lsmchk( 'news_genre_opinion' ); ?>/>
										</label>
										<label><?php echo lsmint( 'opt.news_genre_user_generated' ); ?>
											<input type="checkbox" name="news_genre_user_generated"
											       id="news_genre_user_generated" <?php echo lsmchk( 'news_genre_user_generated' ); ?>/>
										</label>
									</div>

								</div>
							</div>
						</div>
						<div class="form-help-container">
							<div class="form-help">
								<p><?php echo lsmint( 'opt.hlp.news' ); ?></p>
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
								<?php $this->html->select('changeFrequency','change_frequency_frontpage'); ?>
								<label
									for="change_frequency_posts"><?php echo lsmint( 'opt.change_frequency_posts' ); ?>
								</label>
								<?php $this->html->select('changeFrequency','change_frequency_posts'); ?>
								<label
									for="change_frequency_pages"><?php echo lsmint( 'opt.change_frequency_pages' ); ?>
								</label>
								<?php $this->html->select('changeFrequency','change_frequency_pages'); ?>
								<label
									for="change_frequency_authors"><?php echo lsmint( 'opt.change_frequency_authors' ); ?>
								</label>
								<?php $this->html->select('changeFrequency','change_frequency_authors'); ?>
								<label
									for="change_frequency_user_defined"><?php echo lsmint( 'opt.change_frequency_user_defined' ); ?>
								</label>
								<?php $this->html->select('changeFrequency','change_frequency_user_defined'); ?>
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
								<?php $this->html->select('priority','priority_frontpage'); ?>
								<label for="priority_posts"><?php echo lsmint( 'opt.priority_posts' ); ?>
								</label>
								<?php $this->html->select('priority','priority_posts'); ?>
								<label for="priority_pages"><?php echo lsmint( 'opt.priority_pages' ); ?>
								</label>
								<?php $this->html->select('priority','priority_pages'); ?>
								<label for="priority_authors"><?php echo lsmint( 'opt.priority_authors' ); ?>
								</label>
								<?php $this->html->select('priority','priority_authors'); ?>
								<label for="priority_user_defined"><?php echo lsmint( 'opt.priority_user_defined' ); ?>
								</label>
								<?php $this->html->select('priority','priority_user_defined'); ?>
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
										<?php echo $this->html->extraPages; ?>
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
				if ( $this->google->can_send_curl_requests ): ?>
					<div role="tabpanel" class="tab-pane" id="tab_google">
						<?php
						/***********************************************************************************************
						 *                              NOT AUTHENTICATED YET
						 ***********************************************************************************************/
						if ( ! $this->google->helper->is_authenticated() ): ?>
							<div class="form-group">
								<div class="input-group">
									<div class="btn-group">
										<input id="btn-get-google-auth" class="button-primary" type="button"
										       value="<?php echo lsmint( 'btn.google.get_auth' ); ?>"/>
										<input id="google_auth_url" type="hidden"
										       value="<?php echo esc_url( $this->google->helper->get_authentication_url() ); ?>"/>
									</div>

									<div class="btn-group">
										<input type="text" name="google_auth_token"
										       id="google_auth_token"/>
										<input id="btn-google-log-in" class="button-primary" type="submit"
										       name="lti_sitemap_google_auth"
										       value="<?php echo lsmint( 'btn.google.log_in' ); ?>"/>
									</div>
								</div>
								<div class="form-help-container">
									<div class="form-help">
										<p></p>
									</div>
								</div>
							</div>
						<?php
						/***********************************************************************************************
						 *                           AUTHENTICATED
						 ***********************************************************************************************/
						else:
							$site               = $this->google->get_site_info();
							$google_console_url = $this->google->get_console_url();
							$lti_seo_url        = $this->get_lti_seo_url();
							?>
							<div class="form-group">
								<div class="input-group">
									<?php if ( $site->is_listed ): ?>
										<?php if ( $site->sitemap->has_sitemap() ): ?>
											<table class="table">
												<thead>
												<tr>
													<th><?php echo lsmint( 'google.table.hindicator' ); ?></th>
													<th><?php echo lsmint( 'google.table.hvalue' ); ?></th>
												</tr>
												</thead>
												<tbody>
												<tr>
													<td><?php echo lsmint( 'google.table_last_submitted' ); ?></td>
													<td><?php echo lti_mysql_to_date( $site->sitemap->getLastSubmitted() ); ?></td>
												</tr>
												<tr>
													<td><?php echo lsmint( 'google.table_last_downloaded' ); ?></td>
													<td><?php echo lti_mysql_to_date( $site->sitemap->getLastDownloaded() ); ?></td>
												</tr>
												<tr>
													<td><?php echo lsmint( 'google.table_is_processed' ); ?></td>
													<td><?php echo ( $site->sitemap->getIsPending() ) ? lsmint( 'general.no' ) : lsmint( 'general.yes' ); ?></td>
												</tr>
												<tr>
													<td><?php echo lsmint( 'google.table_nb_pages_submitted' ); ?></td>
													<td><?php echo $site->sitemap->getNbPagesSubmitted(); ?></td>
												</tr>
												<tr>
													<td><?php echo lsmint( 'google.table_nb_pages_indexed' ); ?></td>
													<td><?php echo $site->sitemap->getNbPagesIndexed(); ?></td>
												</tr>
												</tbody>
											</table>
											<?php if ( $site->sitemap->is_site_admin() ): ?>
												<div class="btn-group">
													<input id="btn-resubmit" class="button-primary button-submit"
													       name="lti_sitemap_google_submit"
													       type="submit"
													       value="<?php echo lsmint( 'btn.google.resubmit' ); ?>"/>
												</div>
											<?php endif; ?>
											<div class="btn-group">
												<?php if ( $site->sitemap->is_site_admin() ): ?>
													<input id="btn-delete" class="button-primary button-delete"
													       type="submit" name="lti_sitemap_google_delete"
													       value="<?php echo lsmint( 'btn.google.delete' ); ?>"/>
												<?php endif; ?>
												<input id="btn-log-out" class="button-primary" type="submit"
												       name="lti_sitemap_google_logout"
												       value="<?php echo lsmint( 'btn.google.log-out' ); ?>"/>
											</div>
										<?php else: ?>
											<?php if ( $site->sitemap->is_site_admin() ): ?>
												<div class="btn-group">
													<input id="btn-submit" class="button-primary button-submit"
													       type="submit" name="lti_sitemap_google_submit"
													       value="<?php echo lsmint( 'btn.google.submit' ); ?>"/>
												</div>
											<?php endif; ?>
										<?php endif; ?>
									<?php else: ?>
										<div class="info-messages">
											<p><?php echo lsmint( 'msg.google.info1' ); ?></p>

											<p><?php echo lsmint( 'msg.google.info2' ); ?></p>

											<p>
												<a target="_blank"
												   href="<?php echo $lti_seo_url; ?>"><?php echo lsmint( 'msg.google.info4' ); ?></a>
												/
												<a target="_blank"
												   href="<?php echo $google_console_url; ?>"><?php echo lsmint( 'msg.google.info3' ) ?></a>
											</p>
										</div>
									<?php endif; ?>
									<div class="btn-group">
										<input id="btn-log-out" class="button-primary" type="submit"
										       name="lti_sitemap_google_logout"
										       value="<?php echo lsmint( 'btn.google.log-out' ); ?>"/>
									</div>
									<?php if ( ! is_null( $this->google->error ) ): ?>
										<div class="google_errors">
											<p class="error_msg"><?php echo $this->google->error['error']; ?></p>

											<p class="error_msg"><?php echo $this->google->error['google_response']; ?></p>
										</div>
									<?php endif; ?>
								</div>
								<div class="form-help-container">
									<div class="form-help">
										<p></p>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<?php
					/***********************************************************************************************
					 *                                  BING TAB
					 ***********************************************************************************************/
					?>
					<div role="tabpanel" class="tab-pane" id="tab_bing">
						<div class="form-group">
							<div class="input-group">
								<div class="btn-group">
									<input id="btn-bing-submit" class="button-primary" type="button"
									       name="lti_sitemap_google_auth"
									       value="<?php echo lsmint( 'btn.bing.sitemap_submit' ); ?>"/>
									<input id="bing_submission_script" type="hidden"
									       value="<?php echo wp_nonce_url( sprintf( "%s&%s&%s%s",
										       $this->get_admin_slug(),
										       'noheader=true', 'bing_url=',
										       $this->bing->get_submission_url() ),
										       'bing_url_submission', 'lti-sitemap-options' ); ?>"/>
								</div>
							</div>
							<div class="form-help-container">
								<div class="form-help">
									<p></p>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
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