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

//Settings
dcCore::app()->blog->settings->addNamespace('galleryinsert');
$s = dcCore::app()->blog->settings->galleryinsert;

//$selected_rep = !empty($_POST['selected_rep']) ? $_POST['selected_rep'] : null;
$selected_rep = !empty($_POST['selected_rep']) ? $_POST['selected_rep'] : '.';
$p_url = dcCore::app()->admin->getPageURL();

?>
<html>
<head>
  <title>-= Gallery Insert =-</title>
<?php
if (file_exists("js/jquery/jquery-ui-1.8.12.custom.min.js")) {
    echo '<script type="text/javascript" src="js/jquery/jquery-ui-1.8.12.custom.min.js"></script>';  // COMPATIBILITE AVEC dotclear < 2.5
} else {
    echo '<script type="text/javascript" src="js/jquery/jquery-ui.custom.js"></script>';             // COMPATIBILITE AVEC dotclear >= 2.5
}
?>
  <style type="text/css">
	<!--
		#boxes {
			font-family: Arial, sans-serif;
			list-style-type: none;
			margin: 0px;
			padding: 0px;
			width: 100%;
		}
		#boxes img {
			cursor: move;
			vertical-align:middle;
		}
		#boxes li {
			cursor: move;
			position: relative;
			float: left;
			margin: 2px 2px 0px 0px;
			//width: 80px;
			//height: 55px;
			border: 1px solid #000;
			text-align: center;
			//padding-top: 5px;
			padding: 3px;
			background-color: #eeeeff;
		}
		#boxes li.private {
			background-color: #FFD7FF;
		}
		#boxes li.ghost {
			border: 1px solid #000;
			background-color: #ffffff;
		}
	//-->
  </style>

  <script type="text/javascript">
  //<![CDATA[
$(function() {
		// FERMETURE DU POPUP
	$('#cancel_btn').click(function() {
		window.close();
		return false;
	});

		// SELECTION DU REPERTOIRE
	$('#selected_rep').change(function() {
		var select_rep_form = $('#select_rep_form').get(0);
		select_rep_form.submit();
		return false;
	});

		// AJOUT DE LA BALISE POUR LE REPERTOIRE COMPLET
	$('#add_rep_btn').click(function() {
		var add_rep_form = $('#add_rep_form').get(0);

		var options = '';
		if (add_rep_form.display_size.value != 'sq') {
			options += ' thumb=\'' + add_rep_form.display_size.value + '\'';
		}
		if (add_rep_form.link_size.value != 'o') {
			options += ' linkto=\'' + add_rep_form.link_size.value + '\'';
		}

		if (add_rep_form.display_chape[1].checked) options += ' carousel';
		if (add_rep_form.display_chape[2].checked) options += ' galleria';
		if (add_rep_form.display_chape[3].checked) options += ' jgallery';

		if (add_rep_form.showmeta.checked) options += ' showmeta';

		if (add_rep_form.privatepicture_password.value != '') {
			options += ' private=\'' + add_rep_form.privatepicture_password.value + '\'';
		}

		texte = ':::gallery dir=\'' + add_rep_form.selected_rep.value + '\''+ options +'::';

		if ((window.opener.the_toolbar !== undefined)) {
			var tb = window.opener.the_toolbar;
			var data = tb.elements.GalleryInsert.data;
			data.texte = texte;
			tb.elements.GalleryInsert.fncall[tb.mode].call(tb);
		}
		if ((window.opener.CKEDITOR !== undefined)) {
			var editor_name=window.opener.$.getEditorName();
			var editor=window.opener.CKEDITOR.instances[editor_name];
			editor.insertText(texte);
		}

		window.close();
		return false;
	});

		// AJOUT DE LA BALISE POUR LES IMAGES SELECTIONEES
	$('#insert_img_btn').click(function() {
		var select_img_form = $('#add_rep_form').get(0);

		var listimages = select_img_form.selected_rep.value;

		ok=false;
		for(i=0;i<select_img_form.length;i++)
			if(select_img_form.elements[i].name == "checkbox[]" && select_img_form.elements[i].checked == true) {
				ok = true;
				listimages += ';' + select_img_form.elements[i].value;
			}

		if (ok==false) {
			window.alert('<?php echo __('Select at least one picture') ?>');
			return;
		}

		var options = '';
		if (select_img_form.display_size.value != 'sq') {
			options += ' thumb=\'' + select_img_form.display_size.value + '\'';
		}
		if (select_img_form.link_size.value != 'o') {
			options += ' linkto=\'' + select_img_form.link_size.value + '\'';
		}

		if (select_img_form.display_chape[1].checked) options += ' carousel';
		if (select_img_form.display_chape[2].checked) options += ' galleria';
		if (select_img_form.display_chape[3].checked) options += ' jgallery';

		if (select_img_form.showmeta.checked) options += ' showmeta';

		if (select_img_form.privatepicture_password.value != '') {
			options += ' private=\'' + select_img_form.privatepicture_password.value + '\'';
		}

		texte = ':::gallery imgurl=\'' + listimages + '\''+ options +'::';

		if ((window.opener.the_toolbar !== undefined)) {
			var tb = window.opener.the_toolbar;
			var data = tb.elements.GalleryInsert.data;
			data.texte = texte;
			tb.elements.GalleryInsert.fncall[tb.mode].call(tb);
		}
		if ((window.opener.CKEDITOR !== undefined)) {
			var editor_name=window.opener.$.getEditorName();
			var editor=window.opener.CKEDITOR.instances[editor_name];
			editor.insertText(texte);
		}

		window.close();
		return false;
	});

		// SAUVEGARDE LES OPTIONS
	$('#save_param_btn').click(function() {
		var select_img_form = $('#add_rep_form').get(0);
		select_img_form.param_action.value = 'save';
		select_img_form.submit();
		return false;
	});

		// RESTAURE LES OPTIONS
	$('#restore_param_btn').click(function() {
		var select_img_form = $('#add_rep_form').get(0);
		select_img_form.param_action.value = 'restore';
		select_img_form.submit();
		return false;
	});

		// SELECTIONNE TOUTES LES IMAGES
	$('#selectall_btn').click(function() {
		var select_img_form = $('#add_rep_form').get(0);
		for(i=0;i<select_img_form.length;i++)
			if(select_img_form.elements[i].name == "checkbox[]")
				select_img_form.elements[i].checked = true;
		return false;
	});

		// DESELECTIONNE TOUTES LES IMAGES
	$('#selectnone_btn').click(function() {
		var select_img_form = $('#add_rep_form').get(0);
		for(i=0;i<select_img_form.length;i++)
			if(select_img_form.elements[i].name == "checkbox[]")
				select_img_form.elements[i].checked = false;
		return false;
	});

		// INVERSE LA SELECTION DES IMAGES
	$('#invertselect_btn').click(function() {
		var select_img_form = $('#add_rep_form').get(0);
		for(i=0;i<select_img_form.length;i++)
			if(select_img_form.elements[i].name == "checkbox[]")
				select_img_form.elements[i].checked = !select_img_form.elements[i].checked;
		return false;
	});

		// AJOUT DU BOUTON DEROULER POUR LES OPTIONS
	$('h3.options').toggleWithLegend($('div.options'),{cookie:'dcx_galleryinsert_options',speed:200,legend_click:true});

	$('#boxes').sortable({
		forcePlaceholderSize : true,
		placeholder: 'ghost'
	});
	//$('#sortable').disableSelection();

});
  //]]>
  </script>

</head>

<body>
<?php
echo '<h2>-= Gallery Insert =-</h2>';

// Enregistrement / Restauration des options
$param_action = !empty($_POST['param_action']) ? $_POST['param_action'] : null;
if ($param_action == 'save') {
    //dcCore::app()->blog->settings->addNamespace('galleryinsert');
    //dcCore::app()->blog->settings->galleryinsert->put('default_display_size', $_POST['display_size'], 'string');
    //dcCore::app()->blog->settings->galleryinsert->put('default_link_size', $_POST['link_size'], 'string');
    //dcCore::app()->blog->settings->galleryinsert->put('default_display_chape', $_POST['display_chape'], 'string');
    //dcCore::app()->blog->settings->galleryinsert->put('default_showmeta', !empty($_POST['showmeta']), 'boolean');
    $s->put('default_display_size', $_POST['display_size'], 'string');
    $s->put('default_link_size', $_POST['link_size'], 'string');
    $s->put('default_display_chape', $_POST['display_chape'], 'string');
    $s->put('default_showmeta', !empty($_POST['showmeta']), 'boolean');
    $s->put('privatepicture_password', $_POST['privatepicture_password'], 'string');
    echo '<p class="message">' . __('Default options saved.') . '</p>';
}
if ($param_action == 'restore') {
    $_POST['display_size'] = 'sq';
    $_POST['link_size'] = 'o';
    $_POST['display_chape'] = 'default';
    $_POST['showmeta'] = 0;
    $_POST['privatepicture_password'] = '';
    //dcCore::app()->blog->settings->addNamespace('galleryinsert');
    //dcCore::app()->blog->settings->galleryinsert->drop('default_display_size');
    //dcCore::app()->blog->settings->galleryinsert->drop('default_link_size');
    //dcCore::app()->blog->settings->galleryinsert->drop('default_display_chape');
    //dcCore::app()->blog->settings->galleryinsert->drop('default_showmeta');
    $s->drop('default_display_size');
    $s->drop('default_link_size');
    $s->drop('default_display_chape');
    $s->drop('default_showmeta');
    $s->drop('privatepicture_password');
    echo '<p class="message">' . __('Default options restored.') . '</p>';
}

// Formulaire de s�lection des r�pertoires
echo '<form id="select_rep_form" action="' . $p_url . '&popup=1" method="post">';
echo '<h3>' . __('Choose a directory') . '</h3>';

$my_media = new dcMedia();
$d = $my_media->getRootDirs();

$liste_rep['.'] = '.';
foreach ($d as $k => $v) {
    if (!empty($v->relname)) {
        $liste_rep[$v->relname] = $v->relname;
    }
}
// Tri dans l'ordre alpha
//asort($liste_rep);
natcasesort($liste_rep);

echo '<p>' . form::combo('selected_rep', $liste_rep, $selected_rep) . dcCore::app()->formNonce() . '</p>';

if (empty($liste_rep)) {
    echo '<p class="clear form-note warning">' . __('No media directory found') . '</p>';
}

echo '</form>';

if ($selected_rep) {
    // Liste des images du répertoire $selected_rep
    //$my_media = new dcMedia('image');
    $my_media = new dcMedia();
    $my_media->chdir($selected_rep);
    $my_media->getDir();
    $f = $my_media->dir;

    if (empty($f['files'])) {
        echo '<p class="clear form-note warning">' . __('No picture in directory') . '</p>';
    } else {
        echo '<hr/><h3>' . __('Select pictures') . '</h3>';

        echo '<form id="add_rep_form" action="' . $p_url . '&popup=1" method="post">';

        echo '<p>' . form::hidden(['selected_rep'], $selected_rep);
        echo dcCore::app()->formNonce() . '</p>';

        // Afficher les images du répertoire
        echo "\n<ul id='boxes'>\n";
        foreach ($f['files'] as $k => $v) {
            //$media_id = $v->media_id;
            $basename = $v->basename;
            $icone = $v->media_icon;
            $titre = $v->media_title;
            $isprivate = preg_match("|\[private\]|Ui", $titre);
            //$imgicone = '<img width="48" height="48" src="' . $icone . '" alt="' . $titre . '" title="' . $titre . '"/>';
            $imgicone = '<img src="' . $icone . '" alt="' . $titre . '" title="' . $titre . '"/>';
            $checked = isset($_POST['checkbox']) ? in_array($basename, $_POST['checkbox']) : false;
            if ($isprivate) {
                echo '<li class="private">';
            } else {
                echo '<li>';
            }
            echo '<label class="classic">';
            //echo form::checkbox('checkbox[]', $basename, $checked) . $imgicone . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            //echo form::checkbox(array('checkbox[]'), $basename, $checked) . $imgicone . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            echo form::checkbox(['checkbox[]'], $basename, $checked) . $imgicone;
            echo '</label>';
            echo "</li>\n";
        }
        echo '</ul>';

        // Boutons de selection
        echo '<div style="clear:left; padding: 0.5em 0 0 0">';

        echo '<a id="selectall_btn" href="#">' . __('Select all') . '</a> - ';
        echo '<a id="selectnone_btn" href="#">' . __('Select none') . '</a> - ';
        echo '<a id="invertselect_btn" href="#">' . __('Invert selection') . '</a>';

        echo '</div>';

        // Formulaire d'options
        echo '<hr/><h3 class="options">' . __('Options') . '</h3>';

        echo '<div class="options">';

        // Liste les tailles d'images disponibles
        $my_media = new dcMedia('image');
        $liste_sizes = ['original' => 'o'];
        foreach ($my_media->thumb_sizes as $k => $v) {
            if ($v[0] > 0) {
                $liste_sizes[$v[2]] = $k;
            }
        }

        // Chargement des param�tres
        // Si la valeur $_POST existe on prends cette valeur, sinon on charge la valeur sauvegard�e, sinon on prends la valeur par d�faut
        dcCore::app()->blog->settings->addNamespace('galleryinsert');
        $default_display_size = !empty($_POST['display_size']) ? $_POST['display_size'] : $s->default_display_size;
        if (empty($default_display_size)) {
            $default_display_size = 'sq';
        }
        $default_link_size = !empty($_POST['link_size']) ? $_POST['link_size'] : $s->default_link_size;
        if (empty($default_link_size)) {
            $default_link_size = 'o';
        }

        echo '<p><label class="classic">' . __('Displayed size') . ' : ' . form::combo('display_size', $liste_sizes, $default_display_size) . '</label></p>';
        echo '<p class="clear form-note info">' . __('Size of the displayed picture') . '</p>';

        $liste_sizes['none'] = 'none';	// Ajoute 'none' a la liste des tailles disponibles
        echo '<p><label class="classic">' . __('Link to size') . ' : ' . form::combo('link_size', $liste_sizes, $default_link_size) . '</label></p>';
        echo '<p class="clear form-note info">' . __('Choose <i>none</i> for no link') . '</p>';

        $default_display_chape = !empty($_POST['display_chape']) ? $_POST['display_chape'] : $s->default_display_chape;
        if (empty($default_display_chape)) {
            $default_display_chape = 'default';
        }
        if ($default_display_chape == 'carousel' && !$s->carousel_enabled) {
            $default_display_chape = 'default';
        }
        if ($default_display_chape == 'galleria' && !$s->galleria_enabled) {
            $default_display_chape = 'default';
        }
        if ($default_display_chape == 'jgallery' && !$s->jgallery_enabled) {
            $default_display_chape = 'default';
        }

        $t = ($s->carousel_enabled || $s->galleria_enabled || $s->jgallery_enabled) ? '' : ' style="display:none"';
        echo '<p' . $t . '>' . __('View form') . ' : ' . "\n";
        echo '<label class="classic">' . form::radio(['display_chape'], 'default', ($default_display_chape == 'default')) . ' Default</label> ' . "\n";
        $t = ($s->carousel_enabled) ? '' : ' style="display:none"';
        echo '<label ' . $t . ' class="classic">' . form::radio(['display_chape'], 'carousel', ($default_display_chape == 'carousel')) . ' Carousel</label> ' . "\n";
        $t = ($s->galleria_enabled) ? '' : ' style="display:none"';
        echo '<label ' . $t . ' class="classic">' . form::radio(['display_chape'], 'galleria', ($default_display_chape == 'galleria')) . ' Galleria</label>' . "\n";
        $t = ($s->jgallery_enabled) ? '' : ' style="display:none"';
        echo '<label ' . $t . ' class="classic">' . form::radio(['display_chape'], 'jgallery', ($default_display_chape == 'jgallery')) . ' jgallery</label>' . "\n";
        echo '</p>' . "\n";

        $default_showmeta = isset($_POST['showmeta']) ? !empty($_POST['showmeta']) : $s->default_showmeta;
        if (empty($default_showmeta)) {
            $default_showmeta = false;
        }
        echo '<p><label class="classic">' . form::checkbox('showmeta', 1, $default_showmeta) . ' ' . __('Show pictures metadata') . '</label></p>';

        //$default_privatepicture = isset($_POST['privatepicture_enabled']) ? !empty($_POST['privatepicture_enabled']) : $s->privatepicture_enabled;
        //echo '<p><label class="classic" for="privatepicture_enabled">' . form::checkbox('privatepicture_enabled','1',$default_privatepicture) . __('Enable private pictures') . '</label></p>';
        $default_privatepicture_password = isset($_POST['privatepicture_password']) ? $_POST['privatepicture_password'] : $s->privatepicture_password;
        echo '<p><label class="classic" for="privatepicture_password">' . __('Password for private pictures') . " " . form::field('privatepicture_password', 20, 255, $default_privatepicture_password) . '</label></p>';
        echo '<p class="clear form-note info">' . __('Let empty for no private picture management') . '</p>';

        echo '<p><a id="save_param_btn" class="default" href="#">' . __('Save default options') . '</a> - ';
        echo '<a id="restore_param_btn" class="default" href="#">' . __('Restore default options') . '</a></p>';
        echo form::hidden(['param_action'], '');

        echo '</div>';

        // Boutons ajouter rep ou images
        echo '<hr/><p>';
        echo '<a id="add_rep_btn" class="submit" type="submit" href="#">' . __('Add all directory') . '</a> - ';
        echo '<a id="insert_img_btn" class="submit" href="#">' . __('Add selected pictures') . '</a> - ';
        echo '<a id="cancel_btn" class="button" href="#">' . __('Close') . '</a>';
        echo '</p>';
        echo '<p class="clear form-note info">' . __('If you change order of images you must use the button [Add selected pictures]') . '</p>';
        echo '</form>';
    }
}

?>
</body>
</html>
