<?php echo $header; ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<style>
   .dropdowns-ss {
      background: url(image/all-categories.jpg) no-repeat scroll 0px 0px transparent;
      color: transparent !important;
      border: 1px solid #fff;
      height: 43px;
   }
   .dropdowns-oo {
      background: url(image/all-categories-open.jpg) no-repeat scroll 0px 0px transparent;
      color: transparent !important;
      border: 1px solid #fff;
      height: 43px;
   }
   #search_val{border-top-left-radius: 0; border-bottom-left-radius: 0;}
</style>
<?php //echo $left_menu; ?>
<div class="container-fluid">
   <div class="row">
      <div class="col-sm-9" id="store_list_auto">
         <!--<ul class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li> <a href="<?php echo $breadcrumb['href']; ?>"> <?php echo $breadcrumb['text']; ?> </a> </li>
            <?php } ?>
             </ul>-->
         <div class="fixmee"  style="z-index: 12354;">
            <div class="sel-cat-home">
               <div id="search" class="input-group input-groupnew12">
                  <form action="">
                     <?php
                        $path = (isset($_GET['path']) && $_GET['path']) ? $_GET['path']: '';
                        $search = (isset($_GET['searcha']) && $_GET['searcha']) ? $_GET['searcha']: '';
                        $by_search = (isset($_GET['by_search']) && $_GET['by_search']) ? $_GET['by_search']: '';
                        ?>
                     <div class="col-md-2 col-xs-2 allcategory_col">
                        <select name="path" id="path">
                           <option value="">All Category</option>
                           <?php foreach ($categories_seller as $cat_seller) { 
                              $selected = ($cat_seller['category_id'] == $path) ? "selected = selected" : ""; ?>
                           <option value="<?php echo $cat_seller['category_id']; ?>" <?php echo $selected; ?>><?php echo $cat_seller['name']; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <div hidden class="col-md-2 col-xs-3 allcategory_col">
                        <select name="by_search" id="by_search">
                           <option selected value="1" <?php if($by_search == '1'){echo 'selected';} ?>>By All</option>
                           <option value="2" <?php if($by_search == '2'){echo 'selected';} ?>>By Category</option>
                           <option value="3" <?php if($by_search == '3'){echo 'selected';} ?>>By Store/Entity</option>
                           <option value="4" <?php if($by_search == '4'){echo 'selected';} ?>>By Product</option>
                        </select>
                     </div>
                     <div class="col-md-10 col-xs-9 search_col">
                        <div class="form-group">
                           <div class="cols-sm-10">
                              <div class="input-group">
                                 <input type="text" class="form-control" value="<?php echo $search; ?>" name="search" id="searcha"  placeholder="Search"/>
                                 <span class="input-group-addon home-search cursor"><i class="fa fa-search fa" aria-hidden="true"></i></span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
            <!--<div class="sel-cat-home res--mob-ser-main-hme sec-mob" style="background: #fff;">
               <div id="search" class="input-group input-groupnew12">
                  <form action="">
                     <?php
                        $path = (isset($_GET['path']) && $_GET['path']) ? $_GET['path']: '';
                        $search = (isset($_GET['searcha']) && $_GET['searcha']) ? $_GET['searcha']: '';
                        $by_search = (isset($_GET['by_search']) && $_GET['by_search']) ? $_GET['by_search']: '';
                        ?>
                     <div class="col-xs-2 allcategory_col">
                        <select name="path" id="path" class="dropdowns-ss path">
                           <option value="">All Category</option>
                           <?php foreach ($categories_seller as $cat_seller) { 
                              $selected = ($cat_seller['category_id'] == $path) ? "selected = selected" : ""; ?>
                           <option value="<?php echo $cat_seller['category_id']; ?>" <?php echo $selected; ?>><?php echo $cat_seller['name']; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <div class="col-xs-3 allcategory_col">
                        <select name="by_search" id="by_search" class="by_search">
                           <option value="1" <?php if($by_search == '1'){echo 'selected';} ?>>By All</option>
                           <option value="2" <?php if($by_search == '2'){echo 'selected';} ?>>By Category</option>
                           <option value="3" <?php if($by_search == '3'){echo 'selected';} ?>>By Store/Entity</option>
                           <option value="4" <?php if($by_search == '4'){echo 'selected';} ?>>By Product</option>
                        </select>
                     </div>
                     <div class="col-xs-7 search_col">
                        <div class="form-group">
                           <div class="cols-sm-10">
                              <div class="input-group">
                                 <input type="text" class="form-control searcha" value="<?php echo $search; ?>" name="search" id="searcha"  placeholder="Search"/>
                                 <span class="input-group-addon home-search"><i class="fa fa-search fa" aria-hidden="true"></i></span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>-->
         </div>
         <div id="seller_append">
            <?php if (isset($categories)) { 
               if (count($categories) > 0) { ?>
            <?php foreach ($categories as $category) { //print_r($category);?>
            <?php //if ($category['seller']) { ?>
            <?php //foreach (array_chunk($category['seller'], 4) as $sellers) { ?>
            <?php //foreach ($sellers as $seller) { ?>
            <div class="widget main-txt-grp fis-des" id="<?php echo $category['id']; ?>">
               <div class="blog-widget">
                  <div class="widget-post widget_post_mailtitle">
                     <?php 
                     //echo implode(' ', $category);
                     
                     if( $category['image'] != '') { ?>
                     <a href="<?php echo $category['href']; ?>"><img src="image/<?php echo $category['image']; ?>" class="img-responsive" title="<?php echo $category['nickname']; ?>" alt="<?php echo $category['nickname']; ?>" style="width:280px; height:130px;"></a>
                     <?php } else { ?>
                     <a href="<?php echo $category['href']; ?>"><img src="image/no_store_img.jpg" class="img-responsive" title="<?php echo $category['nickname']; ?>" alt="<?php echo $category['nickname']; ?>" style="width:280px; height:130px;"></a>
                     <?php } ?>	
					 <?php if( $category['seller_verified'] != '0') { ?>
                     <img style="position: absolute; margin-left: 250px;" src="image/verified.png" class="img-responsive">
                     <?php } ?>
                     <span class="lenth-dist"><?php if(($category['lat'] && $category['lng']) != '') { ?>
                     <span><a target="_blank" href="https://www.google.co.in/maps/place/<?php echo $category['lat']; ?>,<?php echo $category['lng']; ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> Location on map</a></span> &nbsp; 
		     <?php //if($category['filtered'] =='0') { ?>
                     <?php echo round($category['distance'], 2); ?> Km  
                     <?php //} ?>
                     <?php } ?></span>					 
                     <?php if( $category['nickname'] != '') { ?>
                     <h3><a href="<?php echo $category['href']; ?>" title="<?php echo $category['nickname']; ?>"><?php echo substr($category['nickname'], 0, 25); ?></a></h3>
                     <?php } else { ?>
                     <h3><a href="<?php echo $category['href']; ?>" title="<?php echo $category['name']; ?>"><?php echo substr($category['name'], 0, 25); ?></a></h3>
                     <?php } ?>
                     <?php //if( $category['rating'] != '') { ?>
                     <div>
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                        <?php if ($category['rating'] < $i) { ?>
                        <div class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></div>
                        <?php } else { ?>
                        <div class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></div>
                        <?php } ?>
                        <?php } ?> &nbsp; <?php if($category['review_count'] != ''){ ?><span><?php echo $category['review_count']; ?> Votes</span><?php } else { ?> 0 Votes <?php } ?>
                     </div>
                     <?php //} ?>					
                     
                     <?php if( $category['store_ads'] != '') { ?>
                     <p class="right-str-cnt"><span><?php echo $category['store_ads']; ?> Ad(s)</span></p>
                     <?php } ?>
                     <?php if( $category['telephone'] != '') { ?>
                     <p><i class="fa fa-phone" aria-hidden="true"></i><span> <a href="callto:<?php echo $category['telephone']; ?>"><?php echo $category['telephone']; ?></a></span> </p>
                     <?php } ?>
		     <?php if( $category['seller_address'] != '') { ?>
                     <p class="nw-rule-address"><i class="fa fa-location-arrow" aria-hidden="true"></i><span><?php echo $category['seller_address']; ?></span></p>
                     <?php } ?>
                     <?php //if( $category['description'] != '') { ?>
                     <!--<p><i class="fa fa-info" aria-hidden="true"></i><span><?php echo $category['description']; ?></span> </p>-->
                     <?php //} ?>
                     <?php if((isset($category['filtered']) && $category['filtered'] !='0') ? $category['filtered'] : '') { ?>
                     <div class="filtered_ads">
                        <?php echo "Featured"; ?>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
            <div class="widget main-txt-grp sec-mob seller--inf-pg" id="<?php echo $category['id']; ?>">
               <div class="blog-widget">
                  <div class="widget-post widget_post_mailtitle">
                     <div class="col-xs-12">
                        <div class="row sellr-list">
                           <div class="col-xs-6">			
                              <?php if( $category['seller_verified'] != '0') { ?>
							 <img style="float: right;" src="image/verified.png" class="img-responsive">
							 <?php } ?>
							 <?php if( $category['image'] != '') { ?>
                              <a href="<?php echo $category['href']; ?>" class="seller--rr"><img src="image/<?php echo $category['image']; ?>" class="img-responsive" title="<?php echo $category['nickname']; ?>" alt="<?php echo $category['nickname']; ?>" style="width:140px; height:90px;"></a>
                              <?php } else { ?>
                              <a href="<?php echo $category['href']; ?>" class="seller--rr"><img src="image/no-image.jpg" class="img-responsive" title="<?php echo $category['nickname']; ?>" alt="<?php echo $category['nickname']; ?>" style="width:140px; height:90px;"></a>
                              <?php } ?>
							  
                           </div>
                           <div class="col-xs-6 sell-ret">
                              <?php if( $category['nickname'] != '') { ?>
                              <h3><a href="<?php echo $category['href']; ?>" title="<?php echo $category['nickname']; ?>"><?php echo substr($category['nickname'], 0, 25); ?></a></h3>
                              <?php } else { ?>
                              <h3><a href="<?php echo $category['href']; ?>" title="<?php echo $category['name']; ?>"><?php echo substr($category['name'], 0, 25); ?></a></h3>
                              <?php } ?>
                              <span class="lenth-distt"><?php if(($category['lat'] && $category['lng']) != '') { ?>
                              <span><a target="_blank" href="https://www.google.co.in/maps/place/<?php echo $category['lat']; ?>,<?php echo $category['lng']; ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> Loc on map</a></span> &nbsp; 
			      <?php //if($category['filtered'] =='0') { ?>
                              <?php echo round($category['distance'], 2); ?> Km  
                              <?php //} ?>
                              <?php } ?></span>
                              <?php //if( $category['rating'] != '') { ?>
                              <div>
                                 <?php for ($i = 1; $i <= 5; $i++) { ?>
                                 <?php if ($category['rating'] < $i) { ?>
                                 <div class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></div>
                                 <?php } else { ?>
                                 <div class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></div>
                                 <?php } ?>
                                 <?php } ?> &nbsp; <?php if($category['review_count'] != ''){ ?><span><?php echo $category['review_count']; ?> Votes</span><?php } else { ?> 0 Votes <?php } ?>
                              </div>
                              <?php //} ?>		       		       
                              <?php if( $category['telephone'] != '') { ?>
                              <p><i class="fa fa-phone" aria-hidden="true"></i><span> <a href="callto:<?php echo $category['telephone']; ?>"><?php echo $category['telephone']; ?></a></span> </p>
                              <?php } ?>		       
                           </div>
                        </div>
                     </div>
                     <div class="col-xs-12">
                        <div class="row sellr-list">
                           <div class="col-xs-6">
                              <p class="img--sell-lst"><?php if( $category['store_ads'] != '') { ?><?php echo $category['store_ads']; ?> Ad(s)<?php } ?></p>
                           </div>
                           <div class="col-xs-6">
                              <?php if( $category['seller_address'] != '') { ?>
                              <p class="nw-rule-address"><i class="fa fa-location-arrow" aria-hidden="true"></i><span><?php echo $category['seller_address']; ?></span></p>
                              <?php } ?>
                           </div>
                        </div>
                     </div>
                     <div class="col-xs-12 sell-ret">
                        <?php //if( $category['description'] != '') { ?>
                        <!--<p><i class="fa fa-info" aria-hidden="true"></i><span><?php echo $category['description']; ?></span> </p>-->
                        <?php //} ?>
                        <?php if((isset($category['filtered']) && $category['filtered'] !='0') ? $category['filtered'] : '') { ?>
                        <div class="col-sm-1 filtered_ads">
                           <?php echo "Featured"; ?>
                        </div>
                        <?php } ?>
                     </div>
                  </div>
               </div>
            </div>
            <?php //} } } } } else {?>
            <?php $count = count($categories); ?>	    
               <script>
                  var cat_id = "<?php echo $category['id']; ?>"; 
                  var count = "<?php echo $count; ?>";
                  var first_count = "<?php echo $count; ?>";
               </script>
            <?php } } else {?>
            <p><?php echo $seller_empty; ?></p>
            <div class="buttons clearfix">
               <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
            </div>
            <?php } }?>
            <?php if(isset($categories) && !empty($categories)) { ?>
            <p id="loader_page"><img src="catalog/view/theme/default/image/ajax_loader.gif"></p>
            <?php } ?>
         </div>
      </div>
      <div class="col-sm-3"></div>
   </div>
</div>
<script>
if($(window).width() < 767) {
//For responsive search book
<?php 
if (isset($_GET['path']) && !empty($_GET['path'])) {?>
    var cat_path = <?php echo $_GET['path']; ?>; 
<?php }else{ ?>
    var cat_path = '0';
<?php } ?>
if(cat_path != '0') {
	$("#path").addClass("dropdowns-oo");
} else {
	$("#path").addClass("dropdowns-ss");
}
   
   var fixmeTop = $('.fixmee').offset().top;
   $(window).scroll(function() {
       var currentScroll = $(window).scrollTop();
       if (currentScroll >= fixmeTop) {
           $('.fixmee').css({
               //position: 'sticky',
		    position: 'fixed',
	            top: '0',
		    
               
           });
	   $('.fixmee').css('margin-right', '15px');
       } else {
           $('.fixmee').css({
               position: 'static'
           });
	   $('.fixmee').css('margin-right', '0px');
       }
   });
  } else {
  var fixmeTop = $('.fixmee').offset().top;
   $(window).scroll(function() {
       var currentScroll = $(window).scrollTop();
       if (currentScroll >= fixmeTop) {
           $('.fixmee').css({
               position: 'sticky',
		    //position: 'fixed',
	            top: '0',
		    
               
           });
	   //$('.fixmee').css('margin-right', '15px');
       } else {
           $('.fixmee').css({
               position: 'static'
           });
	   //$('.fixmee').css('margin-right', '0px');
       }
   });
  }
</script>
<script>
   /*$('#updat-bon-det').on('click', function() {
    $.ajax({
   	url: 'index.php?route=seller/seller&path="<?php echo $category_id; ?>"&search="<?php echo $search_val; ?>"&by_search="<?php echo $by_search_val; ?>;',
   	type: 'post',
   	dataType: 'json',
   	//data: $("#top-sign-last").serialize(),
   	success: function(json) {
   		if (json['success']) {
   			$('#reg-sucess').html(json['success']);
   			$("._top-sign-upd").hide();
   			$("._top-log-in").show();
   		}
   	}
    });
    });*/
    /*function store_auto() {
   	$('#store_list_auto').load('index.php?route=seller/seller&path="<?php echo $category_id; ?>"&search="<?php echo $search_val; ?>"&by_search="<?php echo $by_search_val; ?>;');
   }*/
</script>
<script>
   $(document).ready(function(){
      $(".home-search").click(function(){
   		var path = $( "#path" ).val();
   		var search = $( "#searcha" ).val();
   		var by_search = $( "#by_search" ).val();
   		//alert(search);
           	window.location.href = "index.php?route=seller/seller&path="+path+"&searcha="+search+"&by_search="+by_search;
               });
     });
   
   var is_loading = false; 
   //var limit = 6;
   var id = $(".seller_append div:first-child").attr("id");
   var path = $( "#path" ).val();
	var search = $( "#searcha" ).val();
	var by_search = $( "#by_search" ).val();

   $(function() {
      if(id != '' && typeof first_count !== 'undefined' && first_count > 3) {
      	$(window).scroll(function() {
            if (is_loading == false) { 					
               if($(window).scrollTop() + $(window).height() >= $(document).height() - 300) {
               	is_loading = true;
               	$('#loader_page').show(); 
               	$.ajax({
               	    url: "index.php?route=seller/seller/advertisement_seller_list&path="+path+"&searcha="+search+"&by_search="+by_search+"&count="+count,
               	    type: 'GET',				    
               	    success:function(data){	
                  		var str = $.trim(data);

                  		if(str == 'no_record_found') {
                  			$('#loader_page').hide();
                  			is_loading = true;
                  			count = '';						
                  			id = '';
                  		} else {
                  			$('#loader_page').hide();							
                  			$('#seller_append').append(data);										
                  									
                  			is_loading = false;						
                  		}
               	    }
               	});
               }
            }
      	});
      } else { 
         $('#loader_page').hide();
      }
   });	
</script>
<script>
   $('select.dropdowns-ss').on('change', function() {
     if(this.value != '') {
   	$('.dropdowns-ss').css('background', 'url(image/all-categories-open.jpg) no-repeat scroll 0px 0px transparent')
     } else {
   	$('.dropdowns-ss').css('background', 'url(image/all-categories.jpg) no-repeat scroll 0px 0px transparent')
     }
   });   
</script>
<?php echo $footer; ?>