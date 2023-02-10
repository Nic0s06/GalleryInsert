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

// DEFINITION DU BOUTON DE LA BARRE D'EDITION
jsToolBar.prototype.elements.GalleryInsert = {
  type: 'button',
  title: 'Gallery Insert',
  icon: 'index.php?pf=GalleryInsert/media/icon.png',
  fn: {},
  fncall: {},
  open_url: 'plugin.php?p=GalleryInsert&popup=1',
  data: {},
  popup: function () {
    window.the_toolbar = this;
    this.elements.GalleryInsert.data = {};

    var p_win = window.open(
      this.elements.GalleryInsert.open_url,
      'dc_popup',
      'alwaysRaised=yes,dependent=yes,toolbar=yes,height=670,width=650,' +
        'menubar=no,resizable=yes,scrollbars=yes,status=no'
    );
    p_win.focus();
  },
};
// OUVERTURE DU POPUP POUR LES TROIS MODES
jsToolBar.prototype.elements.GalleryInsert.fn.wiki = function () {
  this.elements.GalleryInsert.popup.call(this);
};
jsToolBar.prototype.elements.GalleryInsert.fn.xhtml = function () {
  this.elements.GalleryInsert.popup.call(this);
};
jsToolBar.prototype.elements.GalleryInsert.fn.wysiwyg = function () {
  this.elements.GalleryInsert.popup.call(this);
};
// AJOUT DU TEXTE AU POST POUR LES TROIS MODES
jsToolBar.prototype.elements.GalleryInsert.fncall.wiki = function () {
  var d = this.elements.GalleryInsert.data;
  this.encloseSelection('', '', function () {
    return d.texte;
  });
};
jsToolBar.prototype.elements.GalleryInsert.fncall.xhtml = function () {
  var d = this.elements.GalleryInsert.data;
  this.encloseSelection('', '', function () {
    return d.texte;
  });
};
jsToolBar.prototype.elements.GalleryInsert.fncall.wysiwyg = function () {
  var d = this.elements.GalleryInsert.data;
  temp = document.createTextNode(d.texte);
  this.insertNode(temp);
};
