function autoCompleteCategories() {
  $("input[name='category']").autocomplete({
     source: function(request, response) {
        var elem = '';
        var row = $(this).data('row');

        $.ajax({
           url: 'index.php?route=sellerprofile/sellerprofile/category_autocomplete&filter_name=' +  encodeURIComponent(request),
           dataType: 'json',
           success: function(json) {
              $.map(json, function(item) {
                 elem += "<li class=\"list-category\" data-row="+row+" data-id="+item['category_id']+">"+item['name']+"</li>";
              });

              $('.dropdown-menu_'+ row).empty().html(elem).show();
           }
        });
     },
     minLength: 0
     }).focus(function() {
        var row = $(this).data('row');
        var selected_category = $('#category_hidden_'+row).val();

        console.log('focus');
   });

  return false;
}

function autoCompleteSubcategories() {

  $("input[name='sub_category']").autocomplete({
     source: function(request, response) {
        var elem = '';
        var row = $(this).data('row');
        var category_id = $('#category_hidden_'+row).val();

        $.ajax({
           url: 'index.php?route=sellerprofile/sellerprofile/sub_category_autocomplete&path_id=' + category_id + '&filter_name=' +  encodeURIComponent(request),
           dataType: 'json',
           success: function(json) {
              $.map(json, function(item) {
                 elem += "<li class=\"list-subcategory\" data-row="+row+" data-id="+item['category_id']+">"+item['name']+"</li>";
              });

              $('.dropdown-submenu_'+ row).empty().html(elem).show();
           }
        });
     },
     minLength: 0
     }).focus(function() {
        console.log('focus');
   });

  return false;
}

function getStoreZones(val) {
  $.ajax({
      url: 'index.php?route=account/account/country&country_id=' + val,
      dataType: 'json',
      beforeSend: function() {
        $('select[name=\'store_country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
      },
      complete: function() {
        $('.fa-spin').remove();
      },
      success: function(json) {
        var html = '<option value="0"> --- Please Select --- </option>';

        if (json['zone'] && json['zone'] != '') {
          for (i = 0; i < json['zone'].length; i++) {
            html += '<option value="' + json['zone'][i]['zone_id'] + '"';

            if (json['zone'][i]['zone_id'] == $('#hidden_zone_id').val()) {
              html += ' selected="selected"';
            }

            html += '>' + json['zone'][i]['name'] + '</option>';
          }
        }

        $('#store_zone').html(html);

        setTimeout(function() {
          $('#store_zone').trigger('change');
        }, 500);
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
  });

  return false;
}

function getStoreCities(val) {
  $.ajax({
      url: 'index.php?route=account/account/getCities&city=' + val,
      dataType: 'json',
      success: function(cities) {
        var html = '<option value="0"> --- Please Select --- </option>';

        for (i = 0; i < cities.length; i++) {
          html += '<option value="' + cities[i]['city_name'] + '"';

          if (cities[i]['city_name'] == $('#hidden_store_city').val()) {
            html += ' selected="selected"';
          }

          html += '>' + cities[i]['city_name'] + '</option>';
        }

        $('#store_city').html(html);
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
  });

  return false;
}

function storeAddress() {
 
  try {

    var address1 = $('#store-address-1').val();
    var address2 = $('#store-address-2').val();
    var city = $('#store_city').val();
    var postcode = $('#store-postcode').val();
    var country_name = $('#store_address select[name=\'store_country_id\'] option:selected').text();
    var country_id = $('#store_address select[name=\'store_country_id\'] option:selected').val();
    var zone_name = $('#store_address select[name=\'store_zone\'] option:selected').text();
    var zone_id = $('#store_address select[name=\'store_zone\'] option:selected').val();

    if ($.trim(address1) === '' || address1.length < 3 || address1.length > 150)
      throw "Please fill Address";

    if ($.trim(city) === '' || city.length < 2 || city.length > 100)
      throw "Please fill City";

    if ($.trim(postcode) === '' || postcode.length != 6)
      throw "Please fill Postcode";

    if (country_id === '0')
      throw "Please select Country";

    if (zone_id === '0')
      throw "Please select Region/State";

    $.ajax({
        cache: false,
        url: 'index.php?route=sellerprofile/sellerprofile/saveStoreAddress',
        dataType: 'json',
        method: 'POST',
        data : {
          address_1: address1,
          address_2: address2,
          city: city,
          postcode: postcode,
          country_name: country_name,
          country_id: country_id,
          zone_name: zone_name,
          zone_id: zone_id
        },
        success: function(json) {
          $('#successMsg').empty().hide();
          $('#successMsg').html('<i class="fa fa-check-circle"></i> '+ json.success).show();
	  document.getElementById('store-address-save').disabled = true;
          document.getElementById('store-address-cancel').disabled = true;
          setTimeout(function() {
            $('#store_address').modal('toggle');
            $('#store_address_update').html(json.address);
            $('#successMsg').empty().hide();
	    document.getElementById('store-address-save').disabled = false;
	    document.getElementById('store-address-cancel').disabled = false;
          }, 3000);
        },
      error: function(xhr, ajaxOptions, thrownError) {
        $('#errorMsg').html('<i class="fa fa-times-circle"></i> '+xhr.responseText).show();

        setTimeout(function() {
          $('#errorMsg').empty().hide();
        }, 3000);
      }
    });

  } catch (e) {
    $('#errorMsg').html('<i class="fa fa-times-circle"></i> '+ e).show();

    setTimeout(function() {
      $('#errorMsg').empty().hide();
    }, 3000);
  }

  return false;
}

function storePortals() {
 
  try {

    var website1    = $('#input-website').val();
    var facebook1   = $('#input-facebook').val();
    var twitter1    = $('#input-twitter').val();
    var googleplus1 = $('#input-googleplus').val();
    var instagram1  = $('#input-instagram').val();

    var FBurl = /^(https?:\/\/)?((w{3}\.)?)facebook.com\/.*/i;
    var TWurl = /https?:\/\/twitter\.com\/(#!\/)?[a-z0-9_]+$/i;
    var GPurl = /^(https?:\/\/)?((w{3}\.)?)plus.google.com\/.*/i;
    var INurl = /^(https?:\/\/)?((w{3}\.)?)instagram.com\/.*/i;

    if (website1.length > 500)
      throw "Website address can not be > 500 chars.";
    
    if (facebook1.length > 500)
      throw "Facebook address can not be > 500 chars.";

    if (facebook1.length > 0 && facebook1.length <= 500 && !FBurl.test(facebook1))
      throw "Invalid Facebook url";
    
    if (twitter1.length > 500)
      throw "Twitter address can not be > 500 chars.";

    if (twitter1.length > 0 && twitter1.length <= 500 && !TWurl.test(twitter1))
      throw "Invalid Twitter url";
    
    if (googleplus1.length > 500)
      throw "GooglePlus address can not be > 500 chars.";

    if (googleplus1.length > 0 && googleplus1.length <= 500 && !GPurl.test(googleplus1))
      throw "Invalid GooglePlus url";
    
    if (instagram1.length > 500)
      throw "Instagram address can not be > 500 chars.";

    if (instagram1.length > 0 && instagram1.length <= 500 && !INurl.test(instagram1))
      throw "Invalid Instagram url";

    $.ajax({
        cache: false,
        url: 'index.php?route=sellerprofile/sellerprofile/saveStorePortals',
        dataType: 'json',
        method: 'POST',
        data : {
          website_1: website1,
          facebook_1: facebook1,
          twitter_1: twitter1,
          googleplus_1: googleplus1,
          instagram_1: instagram1,
        },
        success: function(json) {
          $('#portalSuccessMsg').empty().hide();
          $('#portalSuccessMsg').html('<i class="fa fa-check-circle"></i> '+ json.success).show();
	  document.getElementById('store-portals-save').disabled = true;
	  document.getElementById('store-portals-cancel').disabled = true;
          setTimeout(function() {
            $('#store_portals').modal('toggle');
            $('#portalSuccessMsg').empty().hide();
	    document.getElementById('store-portals-save').disabled = false;
	    document.getElementById('store-portals-cancel').disabled = false;
          }, 3000);
        },
        error: function(xhr, ajaxOptions, thrownError) {
          $('#portalErrorMsg').html('<i class="fa fa-times-circle"></i> '+xhr.responseText).show();

        setTimeout(function() {
          $('#portalErrorMsg').empty().hide();
        }, 3000);
      }
    });

  } catch (e) {
    $('#portalErrorMsg').html('<i class="fa fa-times-circle"></i> '+ e).show();

    setTimeout(function() {
      $('#portalErrorMsg').empty().hide();
    }, 3000);
  }

  return false;
}

$(document).ready(function() {

  autoCompleteCategories();
  autoCompleteSubcategories();

  $(document).on('change', 'select[name=\'store_country_id\']', function() {
    getStoreZones($(this).val());

    return false;
  });

  $(document).on('change', '#store_zone', function() {
    getStoreCities($(this).find('option:selected').text());

    return false;
  });

  $(document).on('click', '#openStoreAddress', function() {
    var countryId = $('#hidden_store_countryId').val();

    getStoreZones(countryId);

    $('#store_address').modal('toggle');

    return false;
  });

  $(document).on('click', '#openTermsOfUse', function() {

    $('#terms_of_use').modal({backdrop: 'static', keyboard: false});
    return false;
 
  });

  var termsRead = document.getElementById('terms_read');
  var termsReadOk = document.getElementById('btnTermsReadOk');
  termsRead.onchange = function() {
    termsReadOk.disabled = !this.checked;
  };
  
  $(window).on('load', function() {
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    var c = [];

    for(var i = 0; i < ca.length; i++) {
        c.push(ca[i]);
    }

    if (c.indexOf(' termsPopup=true') < 0) {
      $('#terms_of_use').modal({backdrop: 'static', keyboard: false});
    }  
  });

  $('#btnTermsReadOk').on('click', function () {
    document.getElementById('terms_read').checked = false;
    document.getElementById('btnTermsReadOk').disabled = true;

    var d = new Date();
    d.setTime(d.getTime() + (24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = "termsPopup=true;" + expires + ";path=/";
    $('#terms_of_use').modal('hide');
  });

  $("body").on("click", "#addBtnCategories", function() {
    var ctr = $("#cats tbody").find(".extra").length;
   
		if (ctr < 15) {
			var html  = '<tr id="cat-rows' + ctr + '" class="extra">';
	         html += '<td class="text-left cat">';
	         html += '<input type="text" name="category" data-row="'+ctr+'" id="category_'+ctr+'" value="" placeholder="Type here" class="form-control" autocomplete="off"><ul class="dropdown-menu_'+ctr+'"></ul>';
	         html += '<input type="hidden" id="category_hidden_'+ctr+'" name="category_id" data-row="'+ctr+'" value=""  class="form-control">';
	         html += '</td>';
	         html += '<td class="text-left subcat">';
	         html +=  '<input type="text" name="sub_category" id="sub_category_'+ctr+'" data-row="'+ctr+'" value="" placeholder="Type here" class="form-control" autocomplete="off"><ul class="dropdown-submenu_'+ctr+'"></ul>';
	         html += '<div id="product-category_'+ctr+'" class="well well-sm" style="height: 150px; overflow: auto;">';
	         html += '</div>';
	         html += '</td>';
	         html += '<td class="text-left"><button type="button" onclick="$(\'#cat-rows' + ctr + '\').remove();" class="--minus-btn">-</button></td>';
	         html += '</tr>';

		     $("#cats tbody").append(html);

		     autoCompleteCategories();
		     autoCompleteSubcategories();
		  }

       return false;
   });

	$("body").on("click", ".list-category", function() {
		var category_id = $(this).data('id');
		var row = $(this).data('row');

		$('#category_'+row).val($(this).text());
		$('.dropdown-menu_'+ row).hide();

		$('#category_hidden_'+row+'').val(category_id);

		var elem = '';

    $.ajax({
       url: 'index.php?route=sellerprofile/sellerprofile/sub_category_autocomplete&path_id=' + category_id +'&filter_name=',
       dataType: 'json',
       success: function(json) {
         $('#sub_category_'+row).attr('placeholder','All category');
          if(json.length > 0){
            $('#sub_category_'+row).prop('disabled', false);
            $('#sub_category_'+row).attr('placeholder','Type here');
            $.map(json, function(item) {
              elem += "<li class=\"list-subcategory\" data-row="+row+" data-id="+item['category_id']+">"+item['name']+"</li>";
            });
          }
          else
          {
            $('#sub_category_'+row).prop('disabled', true);
            
          }
          $('.dropdown-submenu_'+ row).empty().html(elem).show();
       }
    });

		return false;
	});

	$("body").on("click", ".list-subcategory", function() {
      var selected_ids = [];
      var subcategory_id = $(this).data('id');
      var row = $(this).data('row');

      $('#sub_category_'+row).val('');
      $('.dropdown-submenu_'+ row).hide();

      selected_ids.push($(this).data('id'));

      $('#product-category_'+row).append('<p><i class="fa fa-minus-circle"></i> ' + $(this).text() + '<input type="hidden" name="product_category[]" id="product_category_'+subcategory_id+'" value=""></p>');

      $('#product_category_'+subcategory_id+'').val(subcategory_id);

      return false;
   });

   $('#button-cat-subcat-save').on('click', function(e) {

      var categories = [];

      $("input[name='category_id'").each(function() {
         var row = $(this).data('row');
         var value = $(this).val();

         if(value) {
            var sub_categories = $("#product-category_"+row+" input[name='product_category[]'").map(function() {
               return $(this).val();
            }).get();

            categories.push({'category' : value, 'sub_categories': sub_categories});
	    
            //$('#storeCatSuccessMsg').html('<i class="fa fa-check-circle"></i> '+ "Categories saved successfully.").show();

	    //setTimeout(function() {
	    //  $('#store_cat').modal('toggle');
	    //  $('#storeCatMsg').empty().hide();
	    //}, 3000);
         }
      });

      $.ajax({
         cache: false,
         type: "POST",
         url: 'index.php?route=sellerprofile/sellerprofile/categories_subcategories_store',
         data: {
            category: JSON.stringify(categories)
         },
        success: function(data) {
            $('#storeCatSuccessMsg').empty().hide();
            $('#storeCatSuccessMsg').html('<i class="fa fa-check-circle"></i> '+ data['success']).show();
            $('#store_cat').scrollTop(0);
	    document.getElementById('button-cat-subcat-save').disabled = true;
            document.getElementById('store-cat-subcat-cancel').disabled = true;
	    document.getElementById('addBtnCategories').disabled = true;
            setTimeout(function() {
              $('#store_cat').modal('toggle');
              $('#storeCatSuccessMsg').empty().hide();
	      document.getElementById('button-cat-subcat-save').disabled = false;
	      document.getElementById('store-cat-subcat-cancel').disabled = false;
	      document.getElementById('addBtnCategories').disabled = false;
            }, 3000);
        },
        error: function(xhr, ajaxOptions, thrownError) {
          $('#storeCatErrorMsg').html('<i class="fa fa-times-circle"></i> '+xhr.responseText).show();
  
          setTimeout(function() {
            $('#storeCatErrorMsg').empty().hide();
          }, 3000);         
        }
      });

      return false;
   });

});
