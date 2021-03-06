

<?php echo $header; ?>
<div class="container">
   <ul class="breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
   </ul>
   <?php if ($success) { ?>
   <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
   <?php } ?>
   <div class="row">
      <?php echo $column_left; ?>
      <?php if ($column_left && $column_right) { ?>
      <?php $class = 'col-sm-6'; ?>
      <?php } elseif ($column_left || $column_right) { ?>
      <?php $class = 'col-sm-9'; ?>
      <?php } else { ?>
      <?php $class = 'col-sm-12'; ?>
      <?php } ?>
      <link href="admin/view/javascript/summernote/summernote.css" rel="stylesheet">
      <script type="text/javascript" src="admin/view/javascript/summernote/summernote.js"></script>
      <div id="content" class="<?php echo $class; ?>">
         <?php echo $content_top; ?>
         <div class="page-header">
            <div class="container-fluid">
            </div>
         </div>
         <div class="container-fluid">
            <div class="pull-right">  
               <a href="<?php echo $insert; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
               <!--<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>-->
            </div>
         </div>
         <div class="container-fluid">
            <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
               <?php echo $error_warning; ?>
               <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } echo $seller_id; ?>
            <div class="panel panel-default">
               <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
               </div>
               <div class="panel-body">
                     <div class="row">
                        <div class="table-responsive">
                           <table class="table table-bordered table-hover">
                              <thead>
                                 <tr>
                                    <td class="text-left"><?php echo $store_name; ?></td>
                                    <td class="text-left"><?php echo $store_address; ?></td>
				    <td class="text-right"><?php echo $column_action; ?></td>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php if ($sellerstore_list) { ?>
                                 <?php foreach ($sellerstore_list as $store_list) { ?>
                                 <tr>
                                    <td class="text-left"><?php echo $store_list['store_name']; ?></td>
				    <td class="text-left"><?php echo $store_list['store_address']; ?></td>
                                    <td class="text-right"><a href="<?php echo $store_list['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
				    <td class="text-center"></td>
                                 </tr>
                                 <?php } ?>
                                 <?php } else { ?>
                                 <tr>
                                    <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                                 </tr>
                                 <?php } ?>
                              </tbody>
                           </table>
                        </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php echo $footer; ?>

