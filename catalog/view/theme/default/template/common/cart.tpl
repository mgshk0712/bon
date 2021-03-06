<div id="cart" class="btn-group btn-block">
  <button type="button" data-toggle="dropdown" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-inverse btn-block btn-lg dropdown-toggle"><span id="cart-total"><?php echo $text_items; ?></span></button>
  <ul class="dropdown-menu pull-right cart-menu">
    <?php if ($products || $vouchers) { ?>
    <li>
      <table class="fis-dess table table-striped bon--cart-prd">
        <?php foreach ($products as $product) { ?>
        <tr>
          <td class="text-center"><?php if ($product['thumb']) { ?>
            <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
            <?php } ?></td>
          <td class="text-left"><a class="prd--cart-name" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
            <?php if ($product['option']) { ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
            <?php } ?>
            <?php } ?>
            <?php if ($product['recurring']) { ?>
            <br />
            - <small><?php echo $text_recurring; ?> <?php echo $product['recurring']; ?></small>
            <?php } ?></td>
          <td class="text-right">x <?php echo $product['quantity']; ?></td>
          <td class="text-right"><?php echo $product['total']; ?></td>
          <td class="text-center"><button type="button" onclick="cart.remove('<?php echo $product['cart_id']; ?>');" title="<?php echo $button_remove; ?>" class="cart--bon-cncl btn-xs"><i class="fa fa-times"></i></button></td>
        </tr>
        <?php } ?>
        <?php //foreach ($vouchers as $voucher) { ?>
        <!-- <tr>
          <td class="text-center"></td>
          <td class="text-left"><?php //echo $voucher['description']; ?></td>
          <td class="text-right">x&nbsp;1</td>
          <td class="text-right"><?php //echo $voucher['amount']; ?></td>
          <td class="text-center text-danger"><button type="button" onclick="voucher.remove('<?php //echo $voucher['key']; ?>');" title="<?php echo $button_remove; ?>" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button></td>
        </tr> -->
        <?php //} ?>
      </table>
      <table class="sec-mobb table table-striped bon--cart-prd">
        <?php foreach ($products as $product) { ?>
        <tr>
          <td class="text-center">
	    <?php if ($product['thumb']) { ?>
            <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
            <?php } ?>
	  </td>
          <td>
			<span class="text-left"><a class="prd--cart-name" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></span><br/>
			<span style="float:left;">x <?php echo $product['quantity']; ?></span>
			<span style="float:right;"><?php echo $product['total']; ?></span>
            <?php if ($product['option']) { ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
            <?php } ?>
            <?php } ?>
            <?php if ($product['recurring']) { ?>
            <br />
            - <small><?php echo $text_recurring; ?> <?php echo $product['recurring']; ?></small>
            <?php } ?>
	  </td>
          <!-- <td class="text-right">x <?php echo $product['quantity']; ?></td>
          <td class="text-right"><?php echo $product['total']; ?></td> -->
          <td class="text-center"><button type="button" onclick="cart.remove('<?php echo $product['cart_id']; ?>');" title="<?php echo $button_remove; ?>" class="cart--bon-cncl btn-xs"><i class="fa fa-times"></i></button></td>
        </tr>
        <?php } ?>
      </table>
    </li>	
    <li>
      <div>
        <table class="table table-bordered">
          <?php foreach ($totals as $total) { 
	  if($total['title'] == "Total") { ?>
          <tr>
            <td class="text-right" style="color: #000;"><strong><?php echo $total['title']; ?></strong></td>
            <td class="text-right" style="color: #000;"><?php echo $total['text']; ?></td>
          </tr>
          <?php } } ?>
        </table>
        <!--<div class="text-right button-container"><a href="<?php echo $cart; ?>" class="addtocart btn btn-primary"><strong><?php echo $text_cart; ?></strong></a><a href="<?php echo $checkout; ?>" class="checkout btn btn-primary"><strong><?php echo $text_checkout; ?></strong></a></div>-->
        <div class="text-right button-container"><a data-toggle="modal" data-target="#under_dev" class="addtocart btn btn-primary"><strong><?php echo $text_cart; ?></strong></a><a data-toggle="modal" data-target="#under_dev" class="checkout btn btn-primary"><strong><?php echo $text_checkout; ?></strong></a></div>
      </div>
    </li>
    <?php } else { ?>
    <li>
      <p class="text-center"><?php echo $text_empty; ?></p>
    </li>
    <?php } ?>
  </ul>
</div>

