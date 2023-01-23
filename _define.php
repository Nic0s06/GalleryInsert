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

$this->registerModule(
    "GalleryInsert", // Name
    "Insert pictures gallery inside your post", // Description
    "Fred, Nicolas Roudaire", // Author
    '0.5.1', // Version
    [
        'permissions' => dcCore::app()->auth->makePermissions([dcAuth::PERMISSION_CONTENT_ADMIN, dcAuth::PERMISSION_USAGE]),
        'type' => 'plugin',
        'dc_min' => '2.24',
        'requires' => [['core', '2.24']],
        'support' => 'https://forum.dotclear.org/viewtopic.php?pid=348953#p348953', // previous one : https://forum.dotclear.org/viewtopic.php?id=45455
        'details' => 'https://plugins.dotaddict.org/dc2/details/GalleryInsert'
    ]
);
