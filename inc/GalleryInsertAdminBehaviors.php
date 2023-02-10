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

class GalleryInsertAdminBehaviors
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

    public static function adminPostEditor(string $editor = '', string $context = '', array $tags = [], string $syntax = 'xhtml'): string
    {
        $settings = dcCore::app()->blog->settings->galleryinsert;
        if (!$settings->galleryinsert_enabled) {
            return '';
        }

        if (!empty($editor) && $editor === 'dcCKEditor') {
            return '';
        }

        return dcPage::jsLoad(dcPage::getPF('GalleryInsert/js/post.js'));
    }

      public static function ckeditorExtraPlugins(ArrayObject $extraPlugins, $context)
      {
          $extraPlugins[] = [
              'name' => 'galleryinsert',
              'button' => 'GalleryInsert',
              'url' => DC_ADMIN_URL . 'index.php?pf=GalleryInsert/js/cke-addon/'
          ];
      }

      public static function coreAfterPostContentFormat($arr)
      {
          $settings = dcCore::app()->blog->settings->galleryinsert;
          if (!$settings->galleryinsert_enabled) {
              return;
          }

          // Chaine de recherche
          //$galerie_reg = "|()?::gallery(.*)::|Ui";
          //$galerie_reg = "|(<p>)?::gallery(.*)::(</p>)?|Ui";
          $galerie_reg = "|(<p>)?::gallery([^:]*)::(</p>)?|i";
          // Remplacement de la chaine par la galerie dans le billet
          $arr['excerpt_xhtml'] = preg_replace_callback($galerie_reg, ['self', 'parseGalleryInsert'], $arr['excerpt_xhtml']);
          $arr['content_xhtml'] = preg_replace_callback($galerie_reg, ['self', 'parseGalleryInsert'], $arr['content_xhtml']);
      }

      protected static function parseGalleryInsert($m)
      {
          $chaine = $m[2];

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

          $galleriagroup = '';
          if (self::$galleria || self::$jgallery) {
              $galleriagroup = "galleriagroup" . rand();
              $s .= '<div id="' . $galleriagroup . '">' . "\n";
          }

          $s .= '<ul class="galleryinsert" style="padding-left:0px">' . "\n"; //TEST

          if (self::$carousel) {
              $carouselgroup = "carouselgroup" . rand();
              $s .= '<li><ul id="' . $carouselgroup . '" style="padding-left:0px">' . "\n";
          }

          if (preg_match("|dir=['\"](.*)['\"]|Ui", $chaine, $reg)) {
              $repertoire = $reg[1];
              $s .= self::createRepGalleryInsert($repertoire, $linkto, $thumbsize);
          }

          if (preg_match("|img=['\"](.*)['\"]|Ui", $chaine, $reg)) {
              $listimages = $reg[1];
              $s .= self::createImgGalleryInsert($listimages, $linkto, $thumbsize);
          }

          if (preg_match("|imgurl=['\"](.*)['\"]|Ui", $chaine, $reg)) {
              $listimages = $reg[1];
              $s .= self::createImgUrlGalleryInsert($listimages, $linkto, $thumbsize);
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
				path:"' . dcPage::getPF('GalleryInsert/js/divbox/players/') . '"
			}
			$("a.' . self::$divboxgroup . '").divbox(opt);
			});</script>' . "\n";
          }

          if (self::$galleria) {
              if (preg_match("|%|", $settings->galleria_width)) {
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

          return $s;
      }

      protected static function createRepGalleryInsert($dir_name, $linkto, $thumbsize)
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

          foreach ($f['files'] as $k => $v) {
              $s .= self::insertImage($v, $linkto, $thumbsize);
          }

          return $s;
      }

      protected static function createImgGalleryInsert($listimages, $linkto, $thumbsize)
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
                  $s .= self::insertImage($f, $linkto, $thumbsize);
              }
          }

          return $s;
      }

      protected static function createImgUrlGalleryInsert($listimages, $linkto, $thumbsize)
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
                      $s .= self::insertImage($j, $linkto, $thumbsize);
                  }
              }
          }

          return $s;
      }

      protected static function insertImage($f, $linkto, $thumbsize)
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
                  if (!file_exists(str_replace($f->dir_url, $f->dir, $image))) {	// Check la présence de la miniature
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
                  $s = self::getMetaData($f->file);
                  if (!empty($s)) {
                      $titre .= ' ' . $s;
                  }
              }
          } else {
              $icone = "index.php?pf=GalleryInsert/media/" . $f->media_type . ".png";
              $image = $f->file_url;
              $titre = $f->media_title;
          }

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

          if (!empty(self::$privatepass) && $isprivate) {
              $txt = '<li class="private">' . $txt . '</li>';
          } else {
              $txt = '<li>' . $txt . '</li>';
          }

          return $txt . "\n";
      }

      protected static function getMetaData($f)
      {
          // Récupération des métadonnées
          $metas = GalleryInsertMeta::imgMeta($f);

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
