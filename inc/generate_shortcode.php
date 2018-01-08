<?php

/***********************************************************
 ************ Generate Shortcode Content *******************
 ***********************************************************/

class GenerateShortcode
{

	private $post_id = null;
	private $post_type = null;
	private $content = null;
	private $start = null;
	private $page = null;
	private $limit = null;

	function __construct($post_id, $page = 1, $limit = null)
	{
 
		$this -> post_id = $post_id;
		$this -> post_type = get_post_meta($this -> post_id, '_cimlap_post_type', true);
		$this -> content = get_post_meta($this -> post_id, '_cimlap_row_structure', true);
		$this -> page = $page; 
		$this -> limit = ($limit == null) ? get_post_meta($this -> post_id, '_cimlap_no_posts', true) : $limit;
		$this -> start = ($this -> page - 1) * $this -> limit;
	}

	function generate_content()
	{  
		$args = array(
			'post_type' => $this -> post_type,
			'post_status' => 'publish',
			'posts_per_page' => $this -> limit,
			'paged' => $this -> page,
		);

		$loop = new WP_Query($args);
		
		
		if ($loop -> have_posts())
		{
			$i = $this->start * $this->limit;

			while ($loop -> have_posts())
			{
				$i++;
				 
				$loop -> the_post();

				$post_data['post_id'] = get_the_ID();
				$post_data['post_title'] = get_the_title();
				$post_data['post_excerpt'] = get_the_excerpt();
				$post_data['post_link'] = get_permalink();
				$post_data['post_date'] = get_the_date();
				$post_data['post_author'] = get_the_author();
				$post_data['post_featured_image'] = get_the_post_thumbnail(get_the_ID(), null, array(
					"class" => "img-responsive",
					"title" => get_the_title()
				));
				$post_data['item_no'] = $i;

				$this -> parse_content($post_data, $i);
				
			}
			
			if($loop->max_num_pages == $this->page){
				$this->hide_load_more();
			}
		}
		else
		{
			
		}
		wp_reset_postdata();
	}

	function hide_load_more(){
		echo '<script>jQuery(".btn-cimlap-more-items").hide();</script>';
	}

	function parse_content($post_data, $i)
	{

		$content = $this -> content;

		//check conditionals
		for ($j = 2; $j < 7; $j++)
		{

			if ($i % $j != 0)
			{
				preg_match_all("/{{if every" . $j . "}}(.*?){{endif}}/s", $content, $result);

				if (count($result[0]))
				{
					foreach ($result[0] as $id => $remove_text)
					{
						$content = str_replace($remove_text, "", $content);
					}
				}
			}

			if ($i % $j == 0)
			{
				preg_match_all("/{{if notevery" . $j . "}}(.*?){{endif}}/s", $content, $result);

				if (count($result[0]))
				{
					foreach ($result[0] as $id => $remove_text)
					{
						$content = str_replace($remove_text, "", $content);
					}
				}
			}
		}

		for ($j = 2; $j < 7; $j++)
		{
			$content = str_replace(array(
				"{{if notevery" . $j . "}}",
				"{{if every" . $j . "}}",
				"{{endif}}"
			), "", $content);
		}

		//replace available items
		$content = str_replace("{{post_title}}", $post_data['post_title'], $content);
		$content = str_replace("{{post_id}}", $post_data['post_id'], $content);
		$content = str_replace("{{post_excerpt}}", $post_data['post_excerpt'], $content);
		$content = str_replace("{{post_date}}", $post_data['post_date'], $content);
		$content = str_replace("{{post_link}}", $post_data['post_link'], $content);
		$content = str_replace("{{post_author}}", $post_data['post_author'], $content);
		$content = str_replace("{{post_featured_image}}", $post_data['post_featured_image'], $content);
		$content = str_replace("{{item_no}}", $post_data['item_no'], $content);

		preg_match_all("/{{(.*?)}}/s", $content, $result);

		if (isset($result[1]) && is_array($result[1]))
		{
			foreach ($result[1] as $rule)
			{
				$with_function = @explode("|", $rule);
				
				if($with_function[0] == "post_featured_image"){
					$content = str_replace('{{'.$rule."}}", $this->get_featuerd_image($post_data['post_id'],$with_function[1],$post_data['post_title']), $content);
				}
				
				$with_meta = @explode(".",$rule);
				
				if($with_meta[0] == "post_meta"){
				  
					if(count($with_meta) == 2){
							
						$meta_value = get_post_meta($post_data['post_id'],$with_meta[1], true); 
						$content = str_replace('{{'.$rule."}}", $meta_value, $content);
						
					}else{
						
						$meta_value = get_post_meta($post_data['post_id'],$with_meta[1]); 
						
						//replace multiarray up to level8
						$val = "";
						if(count($with_meta) == 3){
							$val = $meta_value[0][$with_meta[2]];
						} 
						if(count($with_meta) == 4){
							$val = $meta_value[0][$with_meta[2]][$with_meta[3]];
						} 
						if(count($with_meta) == 5){
							$val = $meta_value[0][$with_meta[2]][$with_meta[3]][$with_meta[4]];
						} 
						if(count($with_meta) == 6){
							$val = $meta_value[0][$with_meta[2]][$with_meta[3]][$with_meta[4]][$with_meta[5]];
						}
						if(count($with_meta) == 7){
							$val = $meta_value[0][$with_meta[2]][$with_meta[3]][$with_meta[4]][$with_meta[5]][$with_meta[6]];
						}
						if(count($with_meta) == 8){
							$val = $meta_value[0][$with_meta[2]][$with_meta[3]][$with_meta[4]][$with_meta[5]][$with_meta[6]][$with_meta[7]];
						}
						
						$content = str_replace('{{'.$rule."}}", $val, $content);
						
					} 
				} 
			}
		}
  
		echo $content;

	}

	function get_featuerd_image($post_id, $size = "content-image", $title = "", $classes = "img-responsive")
	{
		$url = get_the_post_thumbnail_url($post_id, $size);
		$title = ($title == "") ? get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_title', true) : $title;
		$alt = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);

		return '<img src="' . $url . '" title="' . $title . '"  alt="' . $alt . '"  class="' . $classes . '"/>';

	}

}
