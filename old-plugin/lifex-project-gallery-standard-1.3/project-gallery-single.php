<?php get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<main role="main">
		<div class="main__content single__project">
			<div class="wrap group col-xs-12">
				<?php $images = get_field('additional_images'); ?>

				<?php if($images || has_post_thumbnail()): ?>
				<div class="col-xs-12 col-md-6 content-pad-right the__content">
					<h1><?php the_title(); ?></h1>
                    <div class="addthis_inline_share_toolbox" style="margin: 0 auto 24px auto;"><strong>Share This Project:</strong>
                        <?php $pageURI = $_SERVER['REQUEST_URI']; ?>

                        <a class="resp-sharing-button__link" href="https://facebook.com/sharer/sharer.php?u=<?php echo $pageURI ?>" target="_blank" rel="noopener" aria-label="">
                          <div class="resp-sharing-button resp-sharing-button--facebook resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solidcircle">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" version="1.1" role="img" aria-labelledby="at-svg-facebook-1" class="at-icon at-icon-facebook" style="fill: rgb(255, 255, 255); width: 32px; height: 32px;"><title id="at-svg-facebook-1">Facebook</title><g><path d="M22 5.16c-.406-.054-1.806-.16-3.43-.16-3.4 0-5.733 1.825-5.733 5.17v2.882H9v3.913h3.837V27h4.604V16.965h3.823l.587-3.913h-4.41v-2.5c0-1.123.347-1.903 2.198-1.903H22V5.16z" fill-rule="evenodd"></path></g></svg>
                            </div>
                          </div>
                        </a>

                        <a class="resp-sharing-button__link" href="https://twitter.com/intent/tweet/?text=<?php the_title(); ?>&amp;url=<?php echo $pageURI; ?>" target="_blank" rel="noopener" aria-label="">
                          <div class="resp-sharing-button resp-sharing-button--twitter resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solidcircle">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" version="1.1" role="img" aria-labelledby="at-svg-twitter-2" class="at-icon at-icon-twitter" style="fill: rgb(255, 255, 255); width: 32px; height: 32px;"><title id="at-svg-twitter-2">Twitter</title><g><path d="M27.996 10.116c-.81.36-1.68.602-2.592.71a4.526 4.526 0 0 0 1.984-2.496 9.037 9.037 0 0 1-2.866 1.095 4.513 4.513 0 0 0-7.69 4.116 12.81 12.81 0 0 1-9.3-4.715 4.49 4.49 0 0 0-.612 2.27 4.51 4.51 0 0 0 2.008 3.755 4.495 4.495 0 0 1-2.044-.564v.057a4.515 4.515 0 0 0 3.62 4.425 4.52 4.52 0 0 1-2.04.077 4.517 4.517 0 0 0 4.217 3.134 9.055 9.055 0 0 1-5.604 1.93A9.18 9.18 0 0 1 6 23.85a12.773 12.773 0 0 0 6.918 2.027c8.3 0 12.84-6.876 12.84-12.84 0-.195-.005-.39-.014-.583a9.172 9.172 0 0 0 2.252-2.336" fill-rule="evenodd"></path></g></svg>
                            </div>
                          </div>
                        </a>

                        <a class="resp-sharing-button__link" href="https://pinterest.com/pin/create/button/?url=<?php echo $pageURI; ?>&amp;media=<?php echo $pageURI; ?>&amp;description=<?php the_title(); ?>" target="_blank" rel="noopener" aria-label="">
                          <div class="resp-sharing-button resp-sharing-button--pinterest resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solidcircle">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" version="1.1" role="img" aria-labelledby="at-svg-pinterest_share-3" class="at-icon at-icon-pinterest_share" style="fill: rgb(255, 255, 255); width: 32px; height: 32px;"><title id="at-svg-pinterest_share-3">Pinterest</title><g><path d="M7 13.252c0 1.81.772 4.45 2.895 5.045.074.014.178.04.252.04.49 0 .772-1.27.772-1.63 0-.428-1.174-1.34-1.174-3.123 0-3.705 3.028-6.33 6.947-6.33 3.37 0 5.863 1.782 5.863 5.058 0 2.446-1.054 7.035-4.468 7.035-1.232 0-2.286-.83-2.286-2.018 0-1.742 1.307-3.43 1.307-5.225 0-1.092-.67-1.977-1.916-1.977-1.692 0-2.732 1.77-2.732 3.165 0 .774.104 1.63.476 2.336-.683 2.736-2.08 6.814-2.08 9.633 0 .87.135 1.728.224 2.6l.134.137.207-.07c2.494-3.178 2.405-3.8 3.533-7.96.61 1.077 2.182 1.658 3.43 1.658 5.254 0 7.614-4.77 7.614-9.067C26 7.987 21.755 5 17.094 5 12.017 5 7 8.15 7 13.252z" fill-rule="evenodd"></path></g></svg>
                            </div>
                          </div>
                        </a>
                    </div>

					<?php if(get_post_meta($post->ID, 'project_manufacturer', true) || get_post_meta($post->ID, 'project_color', true) || get_the_terms($post->ID, 'project_category')[0]->name): ?><h2><?php echo get_post_meta($post->ID, 'project_manufacturer', true); echo ' '.get_post_meta($post->ID, 'project_color', true); echo ' '.get_the_terms($post->ID, 'project_category')[0]->name; ?></h2><?php endif; ?>
					<?php the_content(); ?>
					<?php $prices = explode('-', get_post_meta($post->ID, 'project_price_range', true)); ?>
					<?php if(isset($prices[0]) && isset($prices[1])):
						$min_price = number_format($prices[0]);
						$max_price = number_format($prices[1]);
					endif; ?>
					<ul>
						<?php if(get_post_meta($post->ID, 'project_id', true)): ?><li><strong>ID:</strong> <?php echo get_post_meta($post->ID, 'project_id', true); ?></li><?php endif; ?>
						<?php if(get_post_meta($post->ID, 'project_sqft', true)): ?><li><strong>Sq Ft:</strong> <?php echo get_post_meta($post->ID, 'project_sqft', true); ?></li><?php endif; ?>
						<?php if(get_post_meta($post->ID, 'project_manufacturer', true)): ?><li><strong>Deck Type:</strong> <?php echo get_post_meta($post->ID, 'project_manufacturer', true); ?></li><?php endif; ?>
						<?php if(get_post_meta($post->ID, 'project_color', true)): ?><li><strong>Color:</strong> <?php echo get_post_meta($post->ID, 'project_color', true); ?></li><?php endif; ?>
						<?php if(!empty($min_price) && !empty($max_price)): ?><li><strong>Price Range:</strong> $<?php echo $min_price; ?> - $<?php echo $max_price; ?></li><?php endif; ?>
					</ul>
				</div>

				<div class="col-xs-12 col-md-6 the__gallery">
					<ul>
                        <?php if (has_post_thumbnail()): ?>
                            <li class="ft-img" data-thumb="<?php echo get_the_post_thumbnail_url(); ?>" style="width:100%;">
                                <a href="<?php echo get_the_post_thumbnail_url(); ?>" data-fancybox="project">
                                    <?php the_post_thumbnail(); ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php $count = 0; ?>
						<?php foreach( $images as $image ): ?>
                            <?php if ($count !== 0) : ?>
							<li data-thumb="<?php echo $image['sizes']['large']; ?>">
								<a href="<?php echo $image['url']; ?>" data-fancybox="project"><img src="<?php echo $image['sizes']['thumbnail']; ?>" alt="<?php if($image['alt']): echo $image['alt']; else: echo $image['title']; endif; ?>" /></a>
							</li>
                            <?php endif; ?>
                            <?php $count++; ?>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php else: ?>
					<h1><?php the_title(); ?></h1>
                    <?php // <div class="addthis_inline_share_toolbox" style="margin: 0 auto 24px auto;"><strong>Share This Project:</strong></div> ?>
					<?php if(get_post_meta($post->ID, 'project_manufacturer', true) || get_post_meta($post->ID, 'project_color', true) || get_the_terms($post->ID, 'project_category')[0]->name): ?><h2><?php echo get_post_meta($post->ID, 'project_manufacturer', true); echo ' '.get_post_meta($post->ID, 'project_color', true); echo ' '.get_the_terms($post->ID, 'project_category')[0]->name; ?></h2><?php endif; ?>
					<?php the_content(); ?>
					<?php $prices = explode('-', get_post_meta($post->ID, 'project_price_range', true)); ?>
					<?php $min_price = number_format($prices[0]); ?>
					<?php $max_price = number_format($prices[1]); ?>
					<ul>
						<?php if(get_post_meta($post->ID, 'project_id', true)): ?><li><strong>ID:</strong> <?php echo get_post_meta($post->ID, 'project_id', true); ?></li><?php endif; ?>
						<?php if(get_post_meta($post->ID, 'project_sqft', true)): ?><li><strong>Sq Ft:</strong> <?php echo get_post_meta($post->ID, 'project_sqft', true); ?></li><?php endif; ?>
						<?php if(get_post_meta($post->ID, 'project_manufacturer', true)): ?><li><strong>Deck Type:</strong> <?php echo get_post_meta($post->ID, 'project_manufacturer', true); ?></li><?php endif; ?>
						<?php if(get_post_meta($post->ID, 'project_color', true)): ?><li><strong>Color:</strong> <?php echo get_post_meta($post->ID, 'project_color', true); ?></li><?php endif; ?>
						<?php if(!empty($min_price) && !empty($max_price)): ?><li><strong>Price Range:</strong> $<?php echo $min_price; ?> - $<?php echo $max_price; ?></li><?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
        
        <div class="black__band__content">
            <div class="wrap">
                <div class="statement">
                    <span>Love This Project? </span>
                    Get Started on Yours Today!
                </div>
                <a class="link" href="<?php echo get_permalink(22); ?>">
                    Contact Us
                </a>
            </div>
        </div>
		
		<div class="main__content wood__background">
			<div class="wrap mini__wrap">
				<h3 class="aligncenter"><?php echo get_post_meta($post->ID, 'project_city', true); ?> <?php echo get_post_meta($post->ID, 'project_state', true); ?> <?php echo get_post_meta($post->ID, 'project_zip', true); ?></h3>
				<iframe src="https://maps.google.it/maps?q=+<?php echo get_post_meta($post->ID, 'project_city', true); ?>%2C<?php echo get_post_meta($post->ID, 'project_state', true); ?>&output=embed" width="100%" height="380px" style="border: none;"></iframe>
			</div>
		</div>
		
	</main>
	
<?php endwhile; wp_reset_query(); ?>

<?php get_footer(); ?>
