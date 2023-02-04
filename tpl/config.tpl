<html>
  <head>
    <title>GalleryInsert</title>
    <?php echo dcPage::jsPageTabs($default_tab);?>
  </head>
  <body>
    <?php echo dcPage::breadcrumb([html::escapeHTML(dcCore::app()->blog->name) => '', __('GalleryInsert') => '']);?>

    <div class="multi-part" id="galleryinsert_settings" title="<?php echo __('Settings'); ?>">
      <form action="<?php echo dcCore::app()->admin->getPageURL();?>" method="post">
        <div class="fieldset">
          <h4><?php echo __('Activation');?></h4>
          <p>
            <label for="galleryinsert_enabled">
              <?php echo form::checkbox('galleryinsert_enabled', '1', $s->galleryinsert_enabled),
              __('Enable GalleryInsert');?>
            </label>
          </p>

          <?php if ($s->galleryinsert_enabled):?>
          <p>
            <label for="divbox_enabled">
              <?php echo form::checkbox('divbox_enabled', '1', $s->divbox_enabled),
              __('Enable Divbox');?>
            </label>
          </p>
          <p>
            <label for="carousel_enabled">
              <?php echo form::checkbox('carousel_enabled', '1', $s->carousel_enabled),
              __('Enable Carousel');?>
            </label>
          </p>
          <p>
            <label for="jgallery_enabled">
              <?php echo form::checkbox('jgallery_enabled', '1', $s->jgallery_enabled),
              __('Enable jgallery');?>
            </label>
          </p>
          <p>
            <label for="galleria_enabled">
              <?php echo form::checkbox('galleria_enabled', '1', $s->galleria_enabled),
              __('Enable Galleria');?>
            </label>
          </p>
          <?php endif;?>
        </div>

	<?php if ($s->galleryinsert_enabled):?>
        <div class="fieldset">
          <h4><?php echo __('Settings');?></h4>
          <p class="field">
            <label for="galleria_width">
              <?php echo __('Galleria Width'), ' ', form::field('galleria_width', 5, 20, $s->galleria_width);?>
            </label>
            <label for="galleria_height">
              <?php echo __('Galleria Height'), ' ', form::field('galleria_height', 5, 20, $s->galleria_height);?>
            </label>
            <label for="galleria_th_size">
              <?php echo __('Galleria Thumbnail Size'), ' ', form::field('galleria_th_size', 5, 20, $s->galleria_th_size);?>
            </label>
          </p>
          <p class="form-note info" id="galleria_help">
            <?php
          echo __('Galleria Width is given in percentage (good for responsive design) or in pixel. Ex : 100% or 500.'), '<br>', 
            __('Galleria Height is given in pixel or in width ratio (good for responsive design). Ex : 450 or 0.5625.'), '<br />',
	    __('Galleria Thumbnail Size is given in pixel. Ex : 70.'); ?>
          </p>
          <p>
            <label for="meta_string">
              <?php echo __('Metadata string');?>
            </label>
            <?php echo form::field('meta_string', 70, 255, $s->meta_string);?>
          </p>
          <p class="form-note info" id="meta_string_help">
            <?php echo __('The metadata string is used to add picture metadata (if available) after the picture title.'), '<br>',
            __('Possible insertions are :');?><br />
            - %Title% <br />- %Description%<br />- %Location%<br />-
            %DateTimeOriginal%<br />- %Make%<br />- %Model%<br />- %Lens%<br />-
            %ExposureProgram%<br />- %Exposure%<br />- %FNumber%<br />-
            %ISOSpeedRatings%<br />- %FocalLength%<br />- %ExposureBiasValue%<br />-
            %MeteringMode%'
          </p>
          <p>
            <label for="style_string">
              <?php echo __('Style string');?>
            </label>
            <?php echo form::textarea('style_string', 100, 5, html::escapeHTML(base64_decode($s->style_string)));?>
          </p>
	</div>	
        <?php endif;?>

	<p>
	  <?php echo form::hidden('p', 'GalleryInsert');?>
          <?php echo dcCore::app()->formNonce();?>
          <input type="submit" name="save" value="<?php echo __('Save configuration');?>" />
        </p>
      </form>
    </div>

    <?php if ($s->galleryinsert_enabled):?>
    <div class="multi-part" id="galleryinsert_list" title="<?php echo __('List of posts with gallery'); ?>">
      <form action="<?php echo dcCore::app()->admin->getPageURL();?>" method="post" id="modal-form">
        <div class="fieldset">
          <p><?php echo $postliste;?></p>
          <?php if (!$postfound):?>
          <p class="form-note warning">
            <?php echo __('No post with gallery found');?>
          </p>
          <?php else: ?>
          <p><?php echo form::hidden(['type'], 'modal');?></p>
          <p>
            <input type="submit" name="doupdate" value="<?php echo __('Update gallery in posts');?>"
            />
            <?php echo dcCore::app()->formNonce();?>
            <?php echo form::hidden('start', '0');?>
          </p>
          <p class="form-note info">
            <?php echo __('Update of posts can be useful after a plugin upgrade, it will regenerate the gallery in all posts.<br />It is recommended to make a backup of your database before... Just in case !');?>
          </p>
	  <?php endif ?>		  
        </div>	
      </form>
    </div>
    <?php endif;?>

    <?php dcPage::helpBlock('galleryinsert');?>    
  </body>
</html>
