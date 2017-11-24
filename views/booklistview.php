<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
// get all library books 
$args = array(
'post_type' => 'library',
'posts_per_page' => -1,
'orderby' => 'title',
'order' => 'ASC'
);

$the_query = new WP_Query( $args );
$all_terms_pub = get_terms( array( 'taxonomy' => 'publisher','hide_empty' => 0, 'fields' => 'names', 'get' => 'all' ) );
?>
<table class="table table-bordered" width="100%" cellspacing="0">
        <tbody>
			<tr>
				<td>Book Name:</td>
				<td><input id="book_name" name="book_name" type="text"></td>
				<td>Author:</td>
				<td><input id="author" name="author" type="text"></td>
			</tr>
			
			<tr>
				<td>Publisher:</td>
				<td>
					<select id="publisher" name="publisher">
						<option value="">Select Publisher</option>
						<?php foreach( $all_terms_pub as $all_terms_publi ): ?>
						<option value="<?php echo $all_terms_publi; ?>"><?php echo $all_terms_publi; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>Rating:</td>
				<td>
					<select id="rating" name="rating">
						<option value="">Select Rating</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td>Price:</td>
				<td colspan="3">
					<span id="ex6SliderMin">1</span> <input id="ex13" type="text" class="span2" value="" data-slider-min="1" data-slider-max="3000" data-slider-step="5" data-slider-value="[1,3000]"/> <span id="ex6SliderMax">3000</span><br/>
					<input type="hidden" value="1" name="min_price" id="min_price"/>
					<input type="hidden" value="3000" name="max_price" id="max_price"/>
				</td>
			</tr>
			
			<tr>
				<td colspan="4" align="center"><input id="search" name="search" value="Search" type="button"></td>
			</tr>
		</tbody>
</table>	
<table id="booklist_table" class="table table-striped table-bordered" width="100%" cellspacing="0">
        <thead>
            <tr>
				<th>No</th>
				<th>Book Name</th>
				<th>Price</th>
				<th>Author</th>
				<th>Publisher</th>
				<th>Rating</th>
			</tr>
        </thead>
        
        <tbody>
            <?php
			$i = 1;
			// The Loop
			while ( $the_query->have_posts() ) : $the_query->the_post();
				
				$authors = get_the_terms(get_the_ID(), 'author');  
				$publishers = get_the_terms(get_the_ID(), 'publisher'); 
				
				$author_text = '';
				$publisher_text = '';
				
				if ( $authors && ! is_wp_error( $authors ) ) : 
				
				foreach ( $authors as $author ) {
					$author_text .= $author->name.",";
				}
				
				endif;
				
				if ( $publishers && ! is_wp_error( $publishers ) ) : 
				
				foreach ( $publishers as $author ) {
					$publisher_text .= $author->name.",";
				}
				
				endif; 
				
				?>
				<tr>
					<td><?php echo $i++; ?></td>
					<td><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></td>
					<td><?php echo '$ '.get_post_meta( get_the_ID() , 'price_meta_box_text', true ); ?></td>
					<td><?php echo substr( $author_text, 0 , -1 ); ?></td>
					<td><?php echo substr( $publisher_text, 0 , -1 ); ?></td>
					<td>
						<?php $start_rating = get_post_meta( get_the_ID() , 'rating_meta_box_select', true ); ?>
						<span style="display:none;" id="star_rating"><?php echo $start_rating; ?></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 1 ) ? "checked" : ""; ?>"></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 2 ) ? "checked" : ""; ?>"></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 3 ) ? "checked" : ""; ?>"></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 4 ) ? "checked" : ""; ?>"></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 5 ) ? "checked" : ""; ?>"></span>
					</td>
				</tr>
				<?php 	
					endwhile;
					// Reset Query
					wp_reset_query();
				?>
        </tbody>
    </table>
	<script>
	(function( $ ) {
	 
		"use strict";
		
		/* Custom filtering for listing */
		$.fn.dataTable.ext.search.push(
			function( settings, data, dataIndex ) {
				
				var min_price = parseFloat( $("#min_price").val(), 10 );
				var max_price = parseFloat( $("#max_price").val(), 10 );
				
				var price = parseFloat( data[2].split(" ")[1] ) || 0;
				
				if ( min_price == "" && max_price == "" )
				{
					return true;
				}
				else if ( min_price == "" && price < max_price )
				{
					return true;
				}
				else if ( min_price < price && "" == max_price )
				{
					return true;
				}
				else if ( min_price < price && price < max_price )
				{
					return true;
				}
				return false;
			}
		);
		 
		$(document).ready( function(){
			
			// ranger setting 
			
			$("#ex13").slider({
				tooltip: 'always'
			});
			$("#ex13").on("slide slideStop", function(slideEvt) {
				var range_val = slideEvt.value;
				$("#ex6SliderMin").text(range_val.toString().split(',')[0]);	
				$("#ex6SliderMax").text(range_val.toString().split(',')[1]);	
				$("#min_price").val(range_val.toString().split(',')[0]);
				$("#max_price").val(range_val.toString().split(',')[1]);
			});
			
			// start datatable
		 
			var table = $('#booklist_table').DataTable({ "dom": "lrtip" , "bLengthChange": false, "responsive": true });
	 
			$("#search").on('click', function () {
				
				var book_name = $('#book_name').val();
				var author = $('#author').val();
				var publisher = $("#publisher option:selected").val();
				var rating = parseInt( $("#rating option:selected").val() ) || 0;
				
				if( book_name != "" )
					table.columns(1).search( book_name, true, false, true );
				else
					table.columns(1).search( '', true, false, true );
				
				if( author != "" )
					table.columns(3).search( author, true, false, true );
				else
					table.columns(3).search( '', true, false, true );
				
				if( publisher != "" )
					table.columns(4).search( publisher, true, false, true );
				else
					table.columns(4).search( '', true, false, true );
				
				if( rating != 0 )
					table.columns(5).search( rating, true, false, true );
				else
					table.columns(5).search( '', true, false, true );
				
				table.draw();
				
			} );
			
			 
		} ); 
	 
	})(jQuery);
	</script>