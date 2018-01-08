<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alexc
 * Date: 4/27/17
 * Time: 6:22 PM
 * To change this template use File | Settings | File Templates.
 */

     $filter_data = array();
     $filter_data['relation'] = 'AND';
 
     $args=array(
        'post_type' => 'realisations',
        'post_status'   => 'publish',
        'posts_per_page' => 9,
        'paged' => $_REQUEST['paged'],
    );
	
	if($typeid > 0){
		$filter_data[] = array(
                'operator' => 'IN',
                'taxonomy' => 'realisations_types',
                'field' => 'term_id',
                'terms' => $typeid
            );
	}
	if($colorid > 0){
		$filter_data[] = array(
                'operator' => 'IN',
                'taxonomy' => 'realisations_colors',
                'field' => 'term_id',
                'terms' => $colorid
            );
	}
	if($materialid > 0){
		$filter_data[] =  array(
                'operator' => 'IN',
                'taxonomy' => 'realisations_materials',
                'field' => 'term_id',
                'terms' => $materialid
            );
	}
	
	if(count($filter_data) > 1){
		$args['tax_query'] = $filter_data;
	} 
 
  
$loop = new WP_Query($args);

if($loop->have_posts()){
	$i = 0;
    ?> 
        <div class="row boxes">

            <?php
            while($loop->have_posts()){

                $loop->the_post();
            ?>
               				    <div class="col-md-4 col-sm-6 col-xs-6 item">
                                    
                                    <a href="<?php echo get_permalink(); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
                                        <img class="img-responsive" src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'spotlights') ?>" title="<?php echo esc_attr(get_the_title()); ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true); ?>"/>
                                    </a> 
                                    
                                    <h4 class="post-title">
                                   		<?php the_title(); ?>
                                   	</h4>
                                   	
                                    <a class="read-more faa-parent animated-hover" href="<?php echo get_permalink(); ?>" title="<?php echo esc_attr(get_the_title()); ?>"><?php pll_e('read more'); ?> <i class="fa fa-chevron-right faa-passing bold" aria-hidden="true"></i></a>
		                           
                                </div>
                                 <?php
			                            $i++;
										
			                            if($i%2==0){
			                                ?>
			                                <div class="clearfix hidden-lg hidden-md">&nbsp;</div>
			                                <?php
			                            }
			                            
			                            if($i%3==0){
			                                ?>
			                                <div class="clearfix hidden-xs hidden-sm">&nbsp;</div>
			                                <?php
			                            } 
				}?>
            <div class="col-xs-12 pagination-border">
                <?= pagination_ajax_realisation($loop->max_num_pages);?>
            </div>
        </div>

    <?php
}else{ ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <h4 class="no-results"><?php pll_e('No results where found'); ?></h4>
            </div>
        </div>
    </div>
<?php
}
?>