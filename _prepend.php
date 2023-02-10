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

Clearbricks::lib()->autoload(
    [
        'GalleryInsertMeta' => __DIR__ . '/inc/GalleryInsertMeta.php',
        'GalleryInsertAdminBehaviors' => __DIR__ . '/inc/GalleryInsertAdminBehaviors.php',
        'GalleryInsertPublicBehaviors' => __DIR__ . '/inc/GalleryInsertPublicBehaviors.php',
    ]
);
