<?php
/*
 *  -- BEGIN LICENSE BLOCK ----------------------------------
 *
 *  This file is part of GalleryInsert, a plugin for DotClear2.
 *
 *  Licensed under the GPL version 2.0 license.
 *  See LICENSE file or
 *  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 *  -- END LICENSE BLOCK ------------------------------------
 */

if (!defined('DC_CONTEXT_ADMIN')) {
    exit;
}

$this_version = dcCore::app()->plugins->moduleInfo('GalleryInsert', 'version');
$installed_version = dcCore::app()->getVersion('GalleryInsert');
if (version_compare((string) $installed_version, $this_version, '>=')) {
    return;
}

// Settings
dcCore::app()->blog->settings->addNamespace('galleryinsert');
$s = dcCore::app()->blog->settings->galleryinsert;

$s->put('galleryinsert_enabled', true, 'boolean', 'Enable GalleryInsert plugin', false, true);
$s->put('divbox_enabled', false, 'boolean', 'Enable divbox for default and carousel gallery', false, true);
$s->put('carousel_enabled', false, 'boolean', 'Enable carousel view for gallery', false, true);
$s->put('jgallery_enabled', false, 'boolean', 'Enable jgallery view for gallery', false, true);
$s->put('galleria_enabled', false, 'boolean', 'Enable galleria view', false, true);
$s->put('galleria_width', '100%', 'string', 'Width of galleria plugin', false, true);
$s->put('galleria_height', '400px', 'string', 'Height of galleria plugin', false, true);
$s->put('galleria_th_size', '60px', 'string', 'Size of galleria thumbnails', false, true);
$s->put('meta_string', '[%FocalLength% - %FNumber% - %Exposure% - ISO:%ISOSpeedRatings%]', 'string', 'String used to show metadata', false, true);
$s->put('style_string', base64_encode('
.galleryinsert li {list-style: none; display: inline;}
.galleryinsert li img {border: 1px solid black; margin: 2px 2px 2px 2px; vertical-align: middle;}
.galleryinsert li.private img {border: 1px dotted black;}
'), 'string', 'String used to style the gallery', false, true);
$s->put('privatepicture_enabled', false, 'boolean', 'Enable private picture management', false, true);

dcCore::app()->setVersion('GalleryInsert', $this_version);

return true;
