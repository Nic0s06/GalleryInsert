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

$s = dcCore::app()->blog->settings->galleryinsert;
$p_url = dcCore::app()->admin->getPageURL();
$default_tab = 'galleryinsert_settings';

if (isset($_POST['save'])) {
    $s->put('galleryinsert_enabled', !empty($_POST['galleryinsert_enabled']));
    $s->put('divbox_enabled', !empty($_POST['divbox_enabled']));
    $s->put('carousel_enabled', !empty($_POST['carousel_enabled']));
    $s->put('galleria_enabled', !empty($_POST['galleria_enabled']));
    $s->put('galleria_width', $_POST['galleria_width']);
    $s->put('galleria_height', $_POST['galleria_height']);
    $s->put('galleria_th_size', $_POST['galleria_th_size']);
    $s->put('meta_string', $_POST['meta_string']);
    $s->put('style_string', base64_encode($_POST['style_string']));
    $s->put('jgallery_enabled', !empty($_POST['jgallery_enabled']));

    http::redirect($p_url . '&config=1');
}

// Updating posts (5 by 5)
if (isset($_REQUEST['doupdate'])) {
    $default_tab = 'galleryinsert_list';
    $params = [];
    $params['post_type'] = '';
    $params['sql'] = "AND (post_content LIKE '%::gallery%' OR post_excerpt LIKE '%::gallery%') ";
    $start = $_REQUEST['start'];
    $lim = 5;
    $params['limit'] = [$start, $lim];

    $postcount = dcCore::app()->blog->getPosts($params, true)->f(0);
    if ($start < $postcount) {
        echo '<p class="clear form-note info">' . sprintf(__('Updating posts %d on %d.'), $start + 1, $postcount) . '</p>';

        $post = dcCore::app()->blog->getPosts($params);
        while ($post->fetch()) {
            $cur = dcCore::app()->con->openCursor(dcCore::app()->prefix . 'post');
            $cur->cat_id = $post->cat_id;
            $cur->post_dt = $post->post_dt;
            $cur->post_format = $post->post_format;
            $cur->post_password = $post->post_password;
            $cur->post_url = $post->post_url;
            $cur->post_lang = $post->post_lang;
            $cur->post_title = $post->post_title;
            $cur->post_excerpt = $post->post_excerpt;
            $cur->post_excerpt_xhtml = $post->post_excerpt_xhtml;
            $cur->post_content = $post->post_content;
            $cur->post_content_xhtml = $post->post_content_xhtml;
            $cur->post_notes = $post->post_notes;
            $cur->post_status = $post->post_status;
            $cur->post_selected = $post->post_selected;
            $cur->post_open_comment = $post->post_open_comment;
            $cur->post_open_tb = $post->post_open_tb;
            dcCore::app()->callBehavior('adminBeforePostUpdate', $cur, $post->post_id);
            dcCore::app()->blog->updPost($post->post_id, $cur);
            dcCore::app()->callBehavior('adminAfterPostUpdate', $cur, $post->post_id);
        }

        $new_url = $p_url . '&doupdate=1&start=' . ($start + $lim);
        echo
        '<script type="text/javascript">' . "\n" .
        "//<![CDATA\n" .
        "window.location = '" . $new_url . "'\n" .
        "//]]>\n" .
        '</script>' .
        '<noscript><p><a href="' . html::escapeURL($new_url) . '">' . __('next') . '</a></p></noscript>';
    } else {
        echo '<p class="message">' . __('Update done') . '</p>';
    }
}

// Messages
if (!empty($_REQUEST['config'])) {
    dcPage::addSuccessNotice(__('Configuration successfully updated'));
}

$params = [];
$params['post_type'] = '';
$params['sql'] = "AND (post_content LIKE '%::gallery%' OR post_excerpt LIKE '%::gallery%') ";

$post = dcCore::app()->blog->getPosts($params);

$postliste = '';
$postfound = false;
while ($post->fetch()) {
    $postfound = true;
    $postliste .= ucfirst($post->post_type) . " " . $post->post_id . " : <a target='_blank' href='" . $post->getURL() . "'>" . $post->post_title . '</a>' . "<BR />\n";
}

include __DIR__ . '/tpl/config.tpl';
