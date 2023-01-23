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

if (!defined('DC_RC_PATH')) {
    return;
}

dcCore::app()->addBehavior('publicHeadContent', [GalleryInsertbehaviorContent::class, 'publicHeadContent']);

//$settings = dcCore::app()->blog->settings->galleryinsert;
// Si la gestion des images privées est activée
// On va cacher ou non les images privées et afficher ou non le formulaire de mot de passe
//if ($settings->galleryinsert_enabled && $settings->privatepicture_enab{
dcCore::app()->addBehavior('publicBeforeContentFilter', [GalleryInsertbehaviorContent::class, 'publicBeforeContentFilter']);
//}

class GalleryInsertbehaviorContent
{
    public static function publicHeadContent()
    {
        // Settings
        $settings = dcCore::app()->blog->settings->galleryinsert;
        if (!$settings->galleryinsert_enabled) {
            return;
        }

        $url = dcCore::app()->blog->getPF('GalleryInsert');

        // Inclusion de la divbox
        if ($settings->divbox_enabled) {
            echo '<script src="' . $url . '/divbox/divbox.js"></script>' . "\n";
            echo '<link type="text/css" rel="stylesheet" href="' . $url . '/divbox/css/divbox.css" />' . "\n";
        }

        // Inclusion de jcarousel
        if ($settings->carousel_enabled) {
            echo '<script src="' . $url . '/tosrus/jquery.tosrus.min.all.js"></script>' . "\n";
            echo '<link rel="stylesheet" type="text/css" href="' . $url . '/tosrus/jquery.tosrus.all.css" />' . "\n";
        }

        // Inclusion de galleria
        if ($settings->galleria_enabled) {
            echo '<script src="' . $url . '/galleria.1.5.7/galleria-1.5.7.min.js"></script>' . "\n";
            echo '<link rel="stylesheet" type="text/css" href="' . $url . '/galleria.1.5.7/themes/classic/galleria.classic.css" />' . "\n";
            echo '<script type="text/javascript">Galleria.loadTheme("' . $url . '/galleria.1.5.7/themes/classic/galleria.classic.min.js")</script>' . "\n";
            $th_size = floatval($settings->galleria_th_size);
            echo '<style type="text/css">
			.galleria-theme-classic .galleria-thumbnails .galleria-image{width:' . ($th_size * 1.3) . 'px;height:' . $th_size . 'px;}
			.galleria-theme-classic .galleria-thumb-nav-left,
			.galleria-theme-classic .galleria-thumb-nav-right{top:' . (($th_size - 40) / 2) . 'px;}
			.galleria-theme-classic .galleria-stage {bottom: ' . ($th_size + 20) . 'px;}
			.galleria-theme-classic .galleria-thumbnails-container {height: ' . ($th_size + 10) . 'px;}
			</style>' . "\n";
        }

        // Inclusion de jgallery
        if ($settings->jgallery_enabled) {
            //echo '<link rel="stylesheet" type="text/css" media="all" href="'.$url.'/jgallery-1.6.2/css/font-awesome.min.css" />'."\n";
            echo '<link rel="stylesheet" type="text/css" media="all" href="' . $url . '/jgallery-1.6.2/css/jgallery.min.css" />' . "\n";
            //echo '<script src="js/jquery-2.0.3.min.js"></script>'."\n";
            echo '<script src="' . $url . '/jgallery-1.6.2/js/jgallery.min.js"></script>' . "\n";
            echo '<script src="' . $url . '/jgallery-1.6.2/js/touchswipe.min.js"></script>' . "\n";
        }

        // Inclusion des styles css perso
        echo '<style type="text/css">' . base64_decode($settings->style_string) . '</style>';

        // Enregistrement ou non en session du mot de passe pour l'affichage des images perso
        if (isset($_REQUEST['showprivatepictures'])) {
            if ($_REQUEST['showprivatepictures']) {
                @session_start();
                $_SESSION['sess_blog_showprivatepictures'] = md5($_REQUEST['showprivatepassword']);
            } else {
                @session_start();
                unset($_SESSION['sess_blog_showprivatepictures']);
            }
        }
    }

    static $gallerynumber = 0;

    public static function publicBeforeContentFilter ($tag, $args)
    {
        if ($tag == "EntryContent" || $tag == "EntryExcerpt") {
            // Si on a au moins une galerie privée
            $reg = "|<!--SHOWPRIVATEFORM ['\"](.*)['\"]-->|Ui";
            if (preg_match($reg, $args[0])) {
                // Traitement de chaque galerie
                $reg = "|<!--DEBUT DE LA GALERIE-->.*<!--FIN DE LA GALERIE-->|ismU";
                $args[0] = preg_replace_callback($reg, ['self', 'ProcessPrivatePictures'], $args[0]);
            }
        }
    }

    protected static function ProcessPrivatePictures($m)
    {
        $chaine = $m[0];
        self::$gallerynumber = self::$gallerynumber + 1;
        //$chaine = "TOTO" . self::$gallerynumber . "TOTO" . $m[0];

        // Recherche le mdp
        $reg = "|<!--SHOWPRIVATEFORM ['\"](.*)['\"]-->|Ui";
        if (preg_match($reg, $chaine, $match)) {
            @session_start();
            // Si on est logué (bon mdp enregistré dans le cookie)
            if (isset($_SESSION['sess_blog_showprivatepictures']) && $_SESSION['sess_blog_showprivatepictures'] == $match[1]) {
                // Affichage du bouton pour cacher les images privées
                //$formname = 'HidePrivatePicture' . rand();
                $formname = 'HidePrivatePicture' . self::$gallerynumber;
                $f = '
				<a name="gallery' . self::$gallerynumber . '"></a>
				<p><a href="javascript:document.getElementById(\'' . $formname . '\').submit();">' . __('Hide private pictures') . '</a></p>
				<form id="' . $formname . '" method="post" action="#gallery' . self::$gallerynumber . '"><p>' . dcCore::app()->formNonce() . form::hidden('showprivatepictures', '0') . '</p></form>';
            } else {
                // Suppression des images privées
                $chaine = preg_replace("|<li class=\"private\".*>.*</li>|Ui", "", $chaine);
                // Affichage du bouton et du formulaire de mot de passe pour afficher les images privées
                //$formname = 'ShowPrivatePicture' . rand();
                $formname = 'ShowPrivatePicture' . self::$gallerynumber;
                $f = '
				<a name="gallery' . self::$gallerynumber . '"></a>
				<p><a href="#" onclick="$(\'#P_' . $formname . '\').toggle(\'fast\');return false;">' . __('+ Show private pictures') . '</a></p>
				<form id="' . $formname . '" method="post" action="#gallery' . self::$gallerynumber . '"><p id ="P_' . $formname . '" style="display:none">
				' . dcCore::app()->formNonce() . form::hidden('showprivatepictures', '1') .
                __('In order to show private pictures you have to enter a password.') . '<br />
				<input size="20" name="showprivatepassword" id="showprivatepassword" maxlength="50" type="password" />
				<a href="javascript:document.getElementById(\'' . $formname . '\').submit();">OK</a>
				</p></form>';
            }
            // Ajout du formulaire en tête de galerie
            $chaine = $f . "\n" . $chaine;
            // Suppression de la ligne SHOWPRIVATEFORM
            $chaine = preg_replace($reg, "", $chaine);

            // S'il ne reste aucune image à afficher on supprime la div galleria (pour ne pas laisser un blanc)
            if (!preg_match('|<li|', $chaine)) {
                $chaine = preg_replace('|<div id="galleria.*</div>|Uis', "", $chaine);
            }
        }

        return $chaine;
    }

    public static function publicBeforeContentFilter2 ($tag, $args)
    {
        if ($tag == "EntryContent" || $tag == "EntryExcerpt") {
            // Extraction de la galerie
            $reg = "|<!--DEBUT DE LA GALERIE-->.*<!--FIN DE LA GALERIE-->|ismU";
            preg_match_all($reg, $args[0], $match);

            $args[0] .= "<textarea rows='20' cols='50'>\n";
            //$args[0] .= $match[1][0];
            foreach ($match[0] as $k => $v) {
                $args[0] .= "$k => $v\n";
            }
            $args[0] .= "</textarea>\n";
        }


        if ($tag == "EntryContent" || $tag == "EntryExcerpt") {
            // Settings
            //$settings = dcCore::app()->blog->settings->galleryinsert;

            // Tant que le formulaire doit-être affiché dans le post
            //$reg = "|<!--SHOWPRIVATEFORM-->|";
            $reg = "|<!--SHOWPRIVATEFORM ['\"](.*)['\"]-->|Ui";
            while (preg_match($reg, $args[0], $match)) {
                @session_start();
                // Si on est logué
                //if (isset($_SESSION['sess_blog_showprivatepictures']) && $_SESSION['sess_blog_showprivatepictures'] == $settings->privatepicture_password) {
                if (isset($_SESSION['sess_blog_showprivatepictures']) && $_SESSION['sess_blog_showprivatepictures'] == $match[1]) {
                    // Affichage du bouton pour cacher les images privées
                    $formname = 'HidePrivatePicture' . rand();
                    $f = '<p><a href="javascript:document.' . $formname . '.submit();">' . __('Hide private pictures') . '</a></p>
					<form name="' . $formname . '" method="post">' . dcCore::app()->formNonce() . form::hidden('showprivatepictures', '0') . '</form>';
                } else {
                    // Suppression des images privées
                    //$args[0] = preg_replace("|<li private=\"1\".*>.*</li>|Ui", "", $args[0]);
                    $args[0] = preg_replace("|<li class=\"private\".*>.*</li>|Ui", "", $args[0]);
                    // Affichage du bouton et du formulaire de mot de passe pour afficher les images privées
                    $formname = 'ShowPrivatePicture' . rand();
                    $f = '
					<p><a href="#" onclick="$(\'#' . $formname . '\').toggle(\'fast\');return false;">' . __('+ Show private pictures') . '</a></p>
					<form name="' . $formname . '" id="' . $formname . '" method="post" style="display:none">
					' . dcCore::app()->formNonce() . form::hidden('showprivatepictures', '1') .
                    __('In order to show private pictures you have to enter a password.') . '<BR>
					<input size="20" name="showprivatepassword" id="showprivatepassword" maxlength="50" type="password">
					<a href="javascript:document.' . $formname . '.submit();">OK</a>
					<br /><br /></form>';
                }
                $args[0] = preg_replace($reg, $f, $args[0], 1); // On ne place qu'une fois le formulaire par post
            }
        }
    }
}
