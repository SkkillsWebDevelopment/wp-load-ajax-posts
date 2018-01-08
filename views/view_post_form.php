 
<div class="wrap"> 
 <?php global $post; ?>
    <table class="form-table">
        
        <tr valign="top">
        <th scope="row"><?php _e("Number of posts per load");?></th>
        <td><input type="number" name="_cimlap_no_posts" min="1" max="100" value="<?php echo esc_attr( get_post_meta($post->ID, '_cimlap_no_posts', true)); ?>" /></td>
        </tr>
        <?php 
        	$post_types = get_post_types(array("public"=>true));
        	 
        ?>
        <tr valign="top">
        <th scope="row"><?php _e("Post Type");?></th>
        <td>
        	
        	<?php $cimlap_post_type = get_post_meta($post->ID,"_cimlap_post_type", true); ?> 
        	<select name="_cimlap_post_type" >
        		<?php foreach($post_types as $pid => $pname){ ?>
        			<option value="<?php echo $pid; ?>" <?php echo ($cimlap_post_type == $pid) ? 'selected="selected"':''; ?>><?php echo $pname; ?></option>
        		<?php } ?>	
        	</select>
        </td>
        </tr>
         
        
        
        <tr valign="top">
        	<th scope="row"><?php _e("Row structure");?></th>
        	<td>
        		<textarea name="_cimlap_row_structure" style="width:100%;min-height:300px;"><?php echo esc_attr( get_post_meta($post->ID, '_cimlap_row_structure', true)); ?></textarea>
        		<div class="wrap">
        			<table style="width:100%;">
        				<tr>
        					<td valign="top" style="vertical-align:top">
        						<h4><?php _e("Placeholders");?></h4>
        						<hr />
        						<strong>{{post_title}}</strong><br />
        						<strong>{{post_id}}</strong><br />
        						<strong>{{post_date}}</strong><br />
        						<strong>{{post_author}}</strong><br />
        						<strong>{{post_excerpt}}</strong><br />
        						<strong>{{post_link}}</strong><br />
        						<strong>{{post_featured_image}}</strong> or <strong>{{post_featured_image|thumbnail}}</strong><br />
        						<strong>{{post_meta.meta_name}}</strong> or if array <strong>{{post_meta.meta_name.key1.key2}}</strong><br />
        						<strong>{{item_no}}</strong><br />
        					</td>
        					<td valign="top" style="vertical-align:top">
        						<h4><?php _e("Conditionals");?></h4>
        						<hr />
        						<strong>{{if every2}} {{endif}}</strong> or <strong>{{if notevery2}} {{endif}}</strong><br />
        						<strong>{{if every3}} {{endif}}</strong> or <strong>{{if notevery3}} {{endif}}</strong><br />
        						<strong>{{if every4}} {{endif}}</strong> or <strong>{{if notevery4}} {{endif}}</strong><br />
        						<strong>{{if every5}} {{endif}}</strong> or <strong>{{if notevery5}} {{endif}}</strong><br />
        						<strong>{{if every6}} {{endif}}</strong> or <strong>{{if notevery6}} {{endif}}</strong><br />
        					</td>
        				</tr>
        			</table>
        		</div>
        	</td>
        </tr> 
    </table>
     
</div>