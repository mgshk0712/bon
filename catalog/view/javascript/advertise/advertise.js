$(document).ready(function() {

	var advertise_id = $('#advertise_id').val();
	var advertise_image = $('#offer_image_original').val();

	$('#input-descriptio').summernote({height: 300});

	if(advertise_id) {
		$('.img-containers').empty();
		
		var img = new Image();
		var orgImageHeight = 200;
		var orgImageWidth = 1000;
		var imgPath = advertise_image;
		img.onload = function() {
			orgImageHeight = this.height;
			orgImageWidth = this.width;
			if(orgImageWidth<=1000)
				orgImageWidth = 1000;
			if(orgImageHeight<=200)
				orgImageWidth = 200

			//alert(this.width + 'x' + this.height);

			$uploadCrop = $('.img-containers').croppie({
					url: imgPath,
					enableExif: false,
					viewport: {
						width: 992,
						height: 187
					},
					boundary: {
						width: orgImageHeight,
						height: orgImageHeight
					},
					enforceBoundary: true,
				});
			$uploadCrop.croppie('bind', imgPath).then(function(){ 
				$(".cr-boundary").css("width","1000px");
				$(".cr-boundary").css("height","500px");
			});
		}
		
		img.src = imgPath ;	
	}

	$('body').on('click', 'a.thumbnail', function(e) {
		$('.img-containers').empty();

		var img = new Image();
		var orgImageHeight = 200;
		var orgImageWidth = 1000;
		var imgPath = $(this).attr('href');
		img.onload = function() {
			orgImageHeight = this.height;
			orgImageWidth = this.width;
			if(orgImageWidth<=1000)
				orgImageWidth = 1000;
			if(orgImageHeight<=200)
				orgImageWidth = 200

			//alert(this.width + 'x' + this.height);

			$uploadCrop = $('.img-containers').croppie({
					url: imgPath,
					enableExif: false,
					viewport: {
						width: 992,
						height: 187
					},
					boundary: {
						width: orgImageHeight,
						height: orgImageHeight
					},
					enforceBoundary: true,
				});
			$uploadCrop.croppie('bind', imgPath).then(function(){ 
				$(".cr-boundary").css("width","1000px");
				$(".cr-boundary").css("height","500px");
			});
			
			$('#cropping-panel').removeClass('hide');
			$('.crop-bt-top').removeClass('hide');
		}
		
		img.src = imgPath ;	

		return false;
	});

	$('#getCroppedTop,#getCroppedBottom').on('click', function (ev) {
		$('#cropped_result').show();
		
	   	$uploadCrop.croppie('result', {
			type: 'canvas',
			size: 'viewport',
			format: 'jpeg'
	  	}).then(function (resp) {

	  		$('#croppedImage img').attr('src', resp);
	  		var resut_str = resp.replace("data:image/jpeg;base64,", "");
	  		$("#image_crop").attr("value", resut_str);

			$('#img-containers').hide();
			$('#getCroppedTop').hide();
			$('#getCroppedBottom').hide();
			$(window).scrollTop($('#offer_title').offset().top);
			//window.scrollTo(0, document.body.scrollHeight || document.documentElement.scrollHeight);
			//setTimeout(function(){ $('#img-containers').show() }, 2000);
			//setTimeout(function() {
				//$(window).scrollTop($('#input-description').offset().top);
				//window.scrollTo(0, document.body.scrollHeight || document.documentElement.scrollHeight);
				//window.scrollTo(300, 500);
			//}, 100);  
	 	});
		 
	 	return false;
	});

	$('#agree_tt').change(function() {
		var agree = $(this).is(":checked") ? 'agree' : 'not_agree';
		$('input#agree').val(agree);
		return false;
	});

	$(document).on('click', '#save', function(e) {
		e.preventDefault();

   		try {

   			var title = $('#offer_title').val();
   			var terms = $('input[name="agree_tt"]').is(':checked');
   			var cropped_result = $('#image_crop').val();

   			if ($.trim(title) === '' || title.length < 2 || title.length > 255)
   				throw '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Title must be greater than 3 and less than 255 characters!</div>';

   			if ($('#img-containers').find('div').length === 0)
   				throw '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Please upload advertisement image and crop desired portion for public view</div>';
   			
   			if (cropped_result === '')
   				throw '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Crop the image and confrm the public view</div>';

   			if (terms === false)
   				throw '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Please agree to the terms and conditions mentioned and then save.</div>';

   			$('input#status').val('draft');
   			$("#form-advertise").submit();

   		} catch (e) {
   			$('#validate_msg').empty().html(e).show();

   			$('body, html').animate({scrollTop:$('#content').offset().top}, 'slow');

			$("body").hide();
			window.scrollTo(0, 0);
			setTimeout(function(){ $("body").show() }, 10);
   			setTimeout(function() {
   				$('#validate_msg').hide();
   			}, 10000);
   		}

   		return false;
		// if ($('input[name="agree_tt"]').is(':checked')) {
		// 	if($('input#offer_title').val() != '') {
		// 		$('input#status').val('draft');
		// 		$("#form-advertise").submit();
		// 	} else {
		// 		$('.alert').remove();
		// 		$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Title must be greater than 3 and less than 255 characters!</div>');	
		// 		$('body, html').animate({scrollTop:$('#content').offset().top}, 'slow');
		// 	}
		// } else {
		// 	$('.alert').remove();
		// 	$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Please agree to the terms and conditions mentioned and then save.</div>');	
		// 	$('body, html').animate({scrollTop:$('#content').offset().top}, 'slow');
		// }
   	});

   	$(document).on('click', '#submitt', function(e) {
   		e.preventDefault();

   		try {
 
   			var title = $('#offer_title').val();
   			var terms = $('input[name="agree_tt"]').is(':checked');
   			var cropped_result = $('#image_crop').val();

   			if ($.trim(title) === '' || title.length < 2 || title.length > 255)
   				throw '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Title must be greater than 3 and less than 255 characters!</div>';

   			if ($('#img-containers').find('div').length === 0)
   				throw '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Please upload advertisement image and crop desired portion for public view</div>';

   			if (cropped_result === '')
   				throw '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Crop the image and confrm the public view</div>';

   			if (terms === false)
   				throw '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Please agree to the terms and conditions mentioned and then save.</div>';

   			$('input#status').val('submitted');
   			$("#form-advertise").submit();
			   
   		} catch (e) {
   			$('#validate_msg').empty().html(e).show();
			
			//if(navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)) {           
			//	window.scrollTo(0);
			//} else {
	   		//$('body, html').animate({scrollTop:$('#content').offset().top}, 'slow');
			//}
			$("body").hide();
			window.scrollTo(0, 0);
			setTimeout(function(){ $("body").show() }, 10);
   			setTimeout(function() {
   				$('#validate_msg').hide();
   			}, 10000);
   		}

   		return false;

		// if ($('input[name="agree_tt"]').is(':checked')) {
		// 	if($('input#offer_title').val() != ''){
		// 		$('input#status').val('submitted');
		// 		$("#form-advertise").submit();
		// 	} else {
		// 		$('.alert').remove();
		// 		$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Title must be greater than 3 and less than 255 characters!</div>');	
		// 		$('body, html').animate({scrollTop:$('#content').offset().top}, 'slow');
		// 	}
		// } else {
		// 	$('.alert').remove();
		// 	$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>Please agree to the terms and conditions mentioned and then save.</div>');	
		// 	$('body, html').animate({scrollTop:$('#content').offset().top}, 'slow');
		// }
   	});

   	$('#button-image').on('click', function() {
   		$('#modal-image').remove();
   
   		$.ajax({
   			url: 'index.php?route=sellerproduct/filemanager&target=input-image&thumb=thumb-image',
   			dataType: 'html',
   			success: function(html) {
   				$('body').append('<div id="modal-image" class="modal">' + html + '</div>');
   
   				$('#modal-image').modal('show');
   			}
   		});
   	});

   	$('button[data-event=\'showImageDialog\']').attr('data-toggle', 'image').removeAttr('data-event');

   	$(document).delegate('button[data-toggle=\'image\']', 'click', function() {
	   	$('#modal-image').remove();
	   
	   	$.ajax({
	   		url: 'index.php?route=sellerproduct/filemanager&token=' + getURLVar('token'),
	   		dataType: 'html',
	   		beforeSend: function() {
	   			$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
	   			$('#button-image').prop('disabled', true);
	   		},
	   		complete: function() {
	   			$('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
	   			$('#button-image').prop('disabled', false);
	   		},
	   		success: function(html) {
	   			$('body').append('<div id="modal-image" class="modal">' + html + '</div>');
	   
	   			$('#modal-image').modal('show');
	   		}
	   	});
   });

   	// Image Manager
    $(document).delegate('a[data-toggle=\'image\']', 'click', function(e) {
	   	e.preventDefault();
	   
	   	var element = this;
	   
	   	$(element).popover({
	   		html: true,
	   		placement: 'right',
	   		trigger: 'manual',
	   		content: function() {
	   			return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
   			}
   		});
   
	   	$(element).popover('toggle');
	   
	   	$('#button-image').on('click', function() {
	   		$('#modal-image').remove();
	   
	   		$.ajax({
	   			url: 'index.php?route=sellerproduct/filemanager&token=' + getURLVar('token') + '&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id'),
	   			dataType: 'html',
	   			beforeSend: function() {
	   				$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
	   				$('#button-image').prop('disabled', true);
	   			},
	   			complete: function() {
	   				$('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
	   				$('#button-image').prop('disabled', false);
	   			},
	   			success: function(html) {
	   				$('body').append('<div id="modal-image" class="modal">' + html + '</div>');
	   
	   				$('#modal-image').modal('show');
	   			}
	   		});
	   
	   		$(element).popover('hide');
	   	});
   	});

	$('select').on('change', function() {
       var top = this.value;
       if(top == 6){
   	      $('#adv-size').html('Please uplaod image of size > 200ht * 1000wt');
       } else {
          $('#adv-size').html('Please uplaod image of size > 1000wt * 200ht');
       }
   });
});
