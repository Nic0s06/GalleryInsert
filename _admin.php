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
    dcPage::getPF('GalleryInsert/icon.pvg'),
    preg_match('/plugin.php\?p=GalleryInsert(&.*)?$/', $_SERVER['REQUEST_URI'])
);

dcCore::app()->addBehavior('adminPostHeaders', [GalleryInsertBehaviors::class, 'jsLoad']);
dcCore::app()->addBehavior('adminPageHeaders', [GalleryInsertBehaviors::class, 'jsLoad']);
dcCore::app()->addBehavior('adminRelatedHeaders', [GalleryInsertBehaviors::class, 'jsLoad']);
dcCore::app()->addBehavior('adminDashboardHeaders', [GalleryInsertBehaviors::class, 'jsLoad']);
dcCore::app()->addBehavior('ckeditorExtraPlugins', [GalleryInsertBehaviors::class, 'ckeditorExtraPlugins']);
dcCore::app()->addBehavior('coreAfterPostContentFormat', ['GalleryInsertbehaviorContent', 'coreAfterPostContentFormat']);

class GalleryInsertBehaviors
{
    public static function jsLoad()
    {
        // Settings
        $settings = dcCore::app()->blog->settings->galleryinsert;
        if (!$settings->galleryinsert_enabled) {
            return;
        }

        return
        '<script type="text/javascript" src="index.php?pf=GalleryInsert/post.js"></script>' .
        '<script type="text/javascript">' . "\n" .
        "//<![CDATA[\n" .
        dcPage::jsJson('jsToolBar.prototype.elements.GalleryInsertBehaviors.title', 'GalleryInsert') .
        "\n//]]>\n" .
        "</script>\n";
    }

    public static function ckeditorExtraPlugins(ArrayObject $extraPlugins, $context)
    {
        $extraPlugins[] = [
            'name' => 'galleryinsert',
            'button' => 'GalleryInsert',
            'url' => DC_ADMIN_URL . 'index.php?pf=GalleryInsert/cke-addon/'
        ];
    }
}

class GalleryInsertbehaviorContent
{
    static $divbox = false;		// Affiche-t-on la divbox ?
    static $jgallery = false;	// Affiche-t-on un jgallery ?
    static $galleria = false;	// Affiche-t-on un galleria ?
    static $carousel = false;	// Affiche-t-on un carousel ?
    static $divboxgroup = '';	// Nom de groupe pour la divbox ?
    static $showmeta = false;	// Affiche-t-on les méta dans les titres ?
    static $meta_string;		// Texte d'affichage des métadonnées des images
    static $enableprivatepicture = false; // Prise en compte des images private ?
    static $galleryhasprivate = false;    // Est-ce que la gallerie en cours contient une image privée ?
    static $privatepass = '';

    public static function coreAfterPostContentFormat ($arr)
    {
        // Settings
        $settings = dcCore::app()->blog->settings->galleryinsert;
        if (!$settings->galleryinsert_enabled) {
            return;
        }

        // Chaine de recherche
        //$galerie_reg = "|()?::gallery(.*)::|Ui";
        //$galerie_reg = "|(<p>)?::gallery(.*)::(</p>)?|Ui";
        $galerie_reg = "|(<p>)?::gallery([^:]*)::(</p>)?|i";
        // Remplacement de la chaine par la galerie dans le billet
        $arr['excerpt_xhtml'] = preg_replace_callback($galerie_reg, ['self', 'ParseGalleryInsert'], $arr['excerpt_xhtml']);
        $arr['content_xhtml'] = preg_replace_callback($galerie_reg, ['self', 'ParseGalleryInsert'], $arr['content_xhtml']);
    }

    protected static function ParseGalleryInsert($m)
    {
        $chaine = $m[2];

        // Settings
        $settings = dcCore::app()->blog->settings->galleryinsert;

        // Détection des paramètres
        // Taille de l'image pointée
        if (preg_match("|linkto=['\"](.*)['\"]|Ui", $chaine, $reg)) {
            $linkto = $reg[1];
        } else {
            $linkto = 'orig';
        }

        // Taille de l'image affichée
        if (preg_match("|thumb=['\"](.*)['\"]|Ui", $chaine, $reg)) {
            $thumbsize = $reg[1];
        } else {
            $thumbsize = 'sq';
        }

        self::$divbox = $settings->divbox_enabled;
        self::$showmeta = preg_match("|showmeta|Ui", $chaine);
        self::$galleria = preg_match("|galleria|Ui", $chaine) & $settings->galleria_enabled;
        self::$jgallery = preg_match("|jgallery|Ui", $chaine) & $settings->jgallery_enabled;
        self::$carousel = preg_match("|carousel|Ui", $chaine) & $settings->carousel_enabled;
        self::$divboxgroup = "dbg" . rand();
        self::$meta_string = $settings->meta_string;
        self::$enableprivatepicture = $settings->privatepicture_enabled;
        self::$galleryhasprivate = false;

        // Activation des photos privées
        if (preg_match("|private=['\"](.*)['\"]|Ui", $chaine, $reg)) {
            self::$privatepass = $reg[1];
        } else {
            self::$privatepass = '';
        }

        // Création de la galerie
        $s = "\n<!--DEBUT DE LA GALERIE-->\n";

        //$s .= "<!--SHOWPRIVATEFORM-->\n";

        //$s .= '<span class="galleryinsert">'."\n";

        if (self::$galleria || self::$jgallery) {
            $galleriagroup = "galleriagroup" . rand();
            //$s .= '<div id="'.$galleriagroup.'" style="width: 500px; height: 400px;">'."\n";
            //$s .= '<div id="'.$galleriagroup.'" style="width: '.$settings->galleria_width.'; height: '.$settings->galleria_height.';">'."\n";
            //$s .= '<div id="'.$galleriagroup.'" style="width: '.$settings->galleria_width.';">'."\n";
            $s .= '<div id="' . $galleriagroup . '">' . "\n";
        }

        $s .= '<ul class="galleryinsert" style="padding-left:0px">' . "\n"; //TEST

        if (self::$carousel) {
            $carouselgroup = "carouselgroup" . rand();
            $s .= '<li><ul id="' . $carouselgroup . '" style="padding-left:0px">' . "\n";
        }

        if (preg_match("|dir=['\"](.*)['\"]|Ui", $chaine, $reg)) {
            $repertoire = $reg[1];
            $s .= self::CreateRepGalleryInsert($repertoire, $linkto, $thumbsize);
        }

        if (preg_match("|img=['\"](.*)['\"]|Ui", $chaine, $reg)) {
            $listimages = $reg[1];
            $s .= self::CreateImgGalleryInsert($listimages, $linkto, $thumbsize);
        }

        if (preg_match("|imgurl=['\"](.*)['\"]|Ui", $chaine, $reg)) {
            $listimages = $reg[1];
            $s .= self::CreateImgUrlGalleryInsert($listimages, $linkto, $thumbsize);
        }

        if (self::$carousel) {
            $s .= '</ul></li>';
        }

        $s .= '</ul>' . "\n"; //TEST

        if (self::$carousel) {
            $th_size = floatval($settings->galleria_th_size);
            $s .= '<script type="text/javascript">$(document).ready(function() {
			$("#' . $carouselgroup . '").tosrus({
				infinite : true,
				slides   : {
					width : ' . $th_size . '
				}
			});
			});</script>' . "\n";
        }

        if (self::$divbox && !self::$galleria && !self::$jgallery) {	// La galleria a son propre lightbox
            $s .= '<script type="text/javascript">$(document).ready(function() {
			var opt = {
				width: null,
				height: null,
				speed: "fast",
				caption_number: true,
				path:"' . dcCore::app()->blog->getQmarkURL() . 'pf=' . basename(dirname(__FILE__)) . '/divbox/players/"
			}
			$("a.' . self::$divboxgroup . '").divbox(opt);
			});</script>' . "\n";
        }

        if (self::$galleria) {
            if (preg_match("|%|", $settings->galleria_width)) {
                //$settings->galleria_width = "'".$settings->galleria_width."'";
                $gw = "'" . $settings->galleria_width . "'";
            } else {
                $gw = floatval($settings->galleria_width);
            }
            $gh = floatval($settings->galleria_height);
            $s .= '<script type="text/javascript">$(document).ready(function() {
				Galleria.run("#' . $galleriagroup . '",{imageCrop:false, imagePan:true, lightbox:true, width:' . $gw . ', height:' . $gh . '});
			});</script>';
            $s .= "</div>\n";
        }

        if (self::$jgallery) {
            $s .= '<script type="text/javascript">$(function() {
				$( "#' . $galleriagroup . '" ).jGallery();
			});</script>';
            $s .= "</div>\n";
        }

        //$s .= "</span>\n<!--FIN DE LA GALERIE-->\n";

        if (self::$galleryhasprivate && !empty(self::$privatepass)) {
            $s .= "<!--SHOWPRIVATEFORM '" . md5(self::$privatepass) . "'-->\n";
        }

        $s .= "<!--FIN DE LA GALERIE-->\n";

        //if (self::$galleryhasprivate && !empty(self::$privatepass)) $s = "\n<!--SHOWPRIVATEFORM '" . md5(self::$privatepass) . "'-->" . $s;

        //$s .= '<form name="ShowPrivatePicture" method="post"><a href="javascript:document.ShowPrivatePicture.submit();">afficher les images</a>' . dcCore::app()->formNonce() . form::hidden('showprivate','1') . '</form>';

        return $s;
    }

    protected static function CreateRepGalleryInsert($dir_name, $linkto, $thumbsize)
    {
        $s = '';

        if (self::$galleria || self::$jgallery || !self::$divbox) {	// La galleria n'affiche que les images
            $my_media = new dcMedia('image');
        } else {
            $my_media = new dcMedia();
        }

        // Detection du répertoire
        if (!is_dir($my_media->root . '/' . $dir_name)) {
            return "<!--REPERTOIRE INEXISTANT-->";
        }

			// Liste des images du répertoire media
        $bl = dcCore::app()->blog;			// Solve bug "LOCK TABLES"
        $bl->con->writeLock($bl->prefix . 'media');	// Solve bug "LOCK TABLES"
        $my_media->chdir($dir_name);
        $my_media->getDir();
        $f = $my_media->dir;
        $bl->con->unlock();				// Solve bug "LOCK TABLES"

        //foreach ($f['files'][0] as $k => $v)
        //	$s .= $k . "=" . $v . "<BR>";

        foreach ($f['files'] as $k => $v) {
            $s .= self::InsertImage($v, $linkto, $thumbsize);
        }

        return $s;
    }

    protected static function CreateImgGalleryInsert($listimages, $linkto, $thumbsize)
    {
        $s = '';

        if (self::$galleria || !self::$divbox) {	// La galleria n'affiche que les images
            $my_media = new dcMedia('image');
        } else {
            $my_media = new dcMedia();
        }

        // Liste des media_id
        $media_ids = explode(';', $listimages);

        foreach ($media_ids as $k => $v) {
            if ($f = $my_media->getFile($v)) {
                $s .= self::InsertImage($f, $linkto, $thumbsize);
            }
        }

        return $s;
    }

    protected static function CreateImgUrlGalleryInsert($listimages, $linkto, $thumbsize)
    {
        $s = '';

        if (self::$galleria || !self::$divbox) {	// La galleria n'affiche que les images
            $my_media = new dcMedia('image');
        } else {
            $my_media = new dcMedia();
        }

        $listimages = explode(';', $listimages);
        $dir_name = array_shift($listimages);

        // Detection du répertoire
        if (!is_dir($my_media->root . '/' . $dir_name)) {
            return "<!--REPERTOIRE INEXISTANT-->";
        }

			// Liste des images du répertoire media
        $bl = dcCore::app()->blog;			// Solve bug "LOCK TABLES"
        $bl->con->writeLock($bl->prefix . 'media');
        $my_media->chdir($dir_name);
        $my_media->getDir();
        $f = $my_media->dir;
        $bl->con->unlock();

        foreach ($listimages as $k => $v) {
            foreach ($f['files'] as $i => $j) {
                if ($j->basename == $v) {
                    //foreach ($j as $k => $v) $s .= $k . "=" . $v . "<BR>";
                    $s .= self::InsertImage($j, $linkto, $thumbsize);
                }
            }
        }

        return $s;
    }

    protected static function InsertImage($f, $linkto, $thumbsize)
    {
        if ($f->media_type == 'image') {
            // URL de la miniature
            if ($thumbsize[0] == 'o') {
                $icone = $f->file_url;
            } else {
                if (isset($f->media_thumb[$thumbsize])) {
                    $icone = $f->media_thumb[$thumbsize];
                } else {
                    $icone = '';
                }
                if (!file_exists(str_replace($f->dir_url, $f->dir, $icone))) {	// Check la présence de la miniature
                    $icone = $f->file_url;
                }
            }
            // URL de l'image originale
            if (substr($linkto, 0, 2) == 'no') {
                $image = '';
            } elseif ($linkto[0] == 'o') {
                $image = $f->file_url;
            } else {
                if (isset($f->media_thumb[$thumbsize])) {
                    $image = $f->media_thumb[$thumbsize];
                } else {
                    $image = '';
                }
                if (!file_exists(str_replace($f->dir_url, $f->dir, $image))) {	// Check la pr�sence de la miniature
                    $image = $f->file_url;
                }
            }
            // Titre de l'image
            $titre = $f->media_title;

            // Si le titre fini par .jpg on le supprime
            if (preg_match('/\.jpg$|\.png$|\.gif$/i', $titre)) {
                if (preg_match("|\[private\]|Ui", $titre)) {
                    $titre = '[private]';
                } else {
                    $titre = '';
                }
            }

            // Analyse des métadonnées
            if (self::$showmeta) {
                $s = self::GetMetaData($f->file);
                if (!empty($s)) {
                    $titre .= ' ' . $s;
                }
            }

        //foreach ($f->media_meta as $k => $l)
        //	echo $k."-".$l."<br />";

        //$titre = trim($titre);
        } else {
            //$icone = $f->media_icon;
            $icone = "index.php?pf=GalleryInsert/media/" . $f->media_type . ".png";
            $image = $f->file_url;
            $titre = $f->media_title;
        }

        //$txt = '<img id="galleryinsert_tn" src="' . $icone . '" alt="' . $titre . '" title="' . $titre . '"/>';
        //TEST//$txt = '<img id="galleryinsert_tn" src="' . $icone . '" alt="" title="' . $titre . '"/>';
        // Via plugin moreCSS : #galleryinsert_tn {border: 1px solid black;margin: 0px 3px 0px 0px;}

        // Gestion des images privées
        //if (self::$enableprivatepicture) {
        $isprivate = false;
        if (!empty(self::$privatepass)) {
            $isprivate = preg_match("|\[private\]|Ui", $titre);
            //$isprivate = $isprivate || $f->media_priv;
            if ($isprivate) {
                self::$galleryhasprivate = true;
            }
        }

        $titre = preg_replace("|\[private\]|Ui", "", $titre);
        $titre = trim($titre);

        $txt = '<img src="' . $icone . '" alt="' . $titre . '"/>';

        if ($image != '') {
            $txt = '<a href="' . $image . '" class="' . self::$divboxgroup . '" title="' . $titre . '">' . $txt . '</a>';
        }

        //if (self::$carousel) {
        //		// Si c'est un carousel on force l'affichage à 48px avec un margin à 0
        //	$txt = str_replace('<img', '<img style="margin:0px" width="48" height="48"', $txt);
        //	$txt .= '<span>' . $titre . '</span>';
        //}

        //if (self::$enableprivatepicture && $isprivate) {
        if (!empty(self::$privatepass) && $isprivate) {
            $txt = '<li class="private">' . $txt . '</li>';
        } else {
            $txt = '<li>' . $txt . '</li>';
        }

        return $txt . "\n";
    }

    protected static function GetMetaData($f)
    {
        require_once dirname(__FILE__) . '/inc/class.gimeta.php';

        // Récupération des métadonnées
        $metas = giMeta::imgMeta($f);

        if (!$metas) {
            return '';
        }

        // Chaîne de remplacement
        // %Title%,%Description%,%Location%,%DateTimeOriginal%,%Make%
        // %Model%,%Lens%,%ExposureProgram%,%Exposure%,%FNumber%
        // %ISOSpeedRatings%,%FocalLength%,%ExposureBiasValue%,%MeteringMode%
        //$str = '[%FocalLength% - %FNumber% - %Exposure% - ISO:%ISOSpeedRatings%]';

        // Recherche des paramètres à remplacer
        preg_match_all("|\%(.*)\%|Ui", self::$meta_string, $out);
        // Détermination des valeurs de remplacement
        $t = self::$meta_string;
        foreach ($out[1] as $k => $v) {
            $t = preg_replace('|%' . $v . '%|Ui', $metas[$v][1], $t);
            //$reg[$k] = '|%' . $v . '%|Ui';
            //$rep[$k] = $metas[$v][1];
        }
        return $t;
        //return preg_replace($reg, $rep, self::$meta_string);
    }
}
