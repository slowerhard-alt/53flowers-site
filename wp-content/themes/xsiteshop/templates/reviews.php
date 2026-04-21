<?

defined( 'ABSPATH' ) || exit;

global $product, $reviews;

if(!comments_open()) return;
	
?><div id="reviews"><?
	?><div id="comments"><? 
		
		$reviews = get_comments(array(
			'number' => 500, 
			'order' => 'DESC', 
			'status' => 'approve',  
			'post_id' => $product->id
		));
		
		?><h3><? _e("Отзывы о товаре") ?> «<?=get_the_title() ?>»</h3><?
		
		if($reviews && count($reviews) > 0)
		{
			?><ul class="commentlist"><?
							
				foreach($reviews as $v)
				{
					?><li class="comment"><?
						?><div class="comment_head xs_flex xs_start"><?
							?><div class="name"><?=$v->comment_author ?></div><?
							?><div class="date"><?=xs_date($v->comment_date, true, true)?></div><?
						?></div><?
						?><div class="comment_content"><?
							?><div class="reating"><?
							
								$rating = intval(get_comment_meta($v->comment_ID, 'rating', true));
								
								?><div class="stars<?=$rating && $rating > 0 ? " selected" : ""?>"><?
								
								for($i = 1; $i <= 5; $i++)
								{
									?><span class="star<?=$rating == $i ? " active" : "" ?>"></span><?
								}
								
								?></div><?
								
							?></div><?
							?><div class="text"><?
								echo wpautop($v->comment_content);
							?></div><?
						?></div><?
					?></li><?
				}
			
			?></ul><?
		}
		else
		{
			?><div class="woocommerce-noreviews"><? _e('О данном товаре ещё нет отзывов.') ?></div><?
		}
		
	?></div><?

	if(get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->id))
	{
		?><div id="review_form_wrapper"><?
			?><div id="review_form"><?
				
				$commenter = wp_get_current_commenter();

				$comment_form = array(
					'title_reply' => ($reviews && count($reviews) > 0) ? __('Создать отзыв:') : __('Будьте первым, кто оставил отзыв:'),
					'title_reply_before' => __('<div class="review_title">'),
					'title_reply_after' => __('</div>'),
					'title_reply_to' => __('Ответить на комментарий %s'),
					'comment_notes_before' => '',
					'comment_notes_after' => '',
					'fields' => array(
						'author' => '<p class="comment-form-author">' . '<label class="review_label" for="author">' . __('Ваше имя').' <span class="required">*</span></label> '.
							'<input id="author" name="author" type="text" value="'.esc_attr($commenter['comment_author']).'" size="30" aria-required="true" /></p>',
						'email'  => '<p class="comment-form-email"><label class="review_label" for="email">' . __('Email') . ' <span class="required">*</span></label> ' .
							'<input id="email" name="email" type="email" value="'.esc_attr($commenter['comment_author_email']).'" size="30" aria-required="true" /></p>',
					),
					'label_submit' => __('Оставить отзыв'),
					'class_submit' => __('btn'),
					'logged_in_as' => '',
					'comment_field' => ''
				);
				
				$account_page_url = wc_get_page_permalink('myaccount');
				
				if($account_page_url)
				{
					$comment_form['must_log_in'] = '<p class="must-log-in">'.__('Для отправки отзыва вам необходимо авторизоваться на сайте.').'<br/><a href="'.get_bloginfo('template_directory').'/load/xs_loginform.php?redirect_url='.get_bloginfo('url').$_SERVER['REDIRECT_URL'].'" data-type="ajax" rel="nofollow" class="log-in fancybox btn">Авторизоваться</a></p>';
				}

				if(wc_review_ratings_enabled())
				{
					$comment_form['comment_field'] = '<p class="comment-form-rating xs_flex xs_start xs_middle"><label for="rating">'. __('Оценка: ').'</label><select name="rating" id="rating">
						<option value="">' . __( 'Rate&hellip;', 'woocommerce' ) . '</option>
						<option value="5">' . __( 'Perfect', 'woocommerce' ) . '</option>
						<option value="4">' . __( 'Good', 'woocommerce' ) . '</option>
						<option value="3">' . __( 'Average', 'woocommerce' ) . '</option>
						<option value="2">' . __( 'Not that bad', 'woocommerce' ) . '</option>
						<option value="1">' . __( 'Very Poor', 'woocommerce' ) . '</option>
					</select></p>';
				}

				$comment_form['comment_field'] .= '<p class="comment-form-comment"><label class="review_label" for="comment">' . __( 'Ваш отзыв', 'woocommerce' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

				comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
			
			?></div><?
		?></div><?
	}
	else
	{
		?><p class="woocommerce-verification-required"><? _e('Только зарегистрированные клиенты, которые приобрели этот продукт, могут оставить отзывы.'); ?></p><?
	}

	?><div class="clear"></div><?
?></div><?