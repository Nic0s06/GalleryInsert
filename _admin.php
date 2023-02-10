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
    return;
}

dcCore::app()->menu[dcAdmin::MENU_PLUGINS]->addItem(
    'GalleryInsert',
    dcCore::app()->adminurl->get('admin.plugin.GalleryInsert'),
    dcPage::getPF('GalleryInsert/media/icon.png'),
    preg_match('/plugin.php\?p=GalleryInsert(&.*)?$/', $_SERVER['REQUEST_URI'])
);

dcCore::app()->addBehavior('adminPostEditor', [GalleryInsertAdminBehaviors::class, 'adminPostEditor']);
dcCore::app()->addBehavior('ckeditorExtraPlugins', [GalleryInsertAdminBehaviors::class, 'ckeditorExtraPlugins']);
dcCore::app()->addBehavior('coreAfterPostContentFormat', [GalleryInsertAdminBehaviors::class, 'coreAfterPostContentFormat']);
