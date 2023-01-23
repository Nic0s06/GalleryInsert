# Plugin DotClear - GalleryInsert

# v0.5.1 - 2023-01-23

- Restore icon in menu
- Fix import script in admin
- Add translations. Thanks to Pierre Van Glabeke

# v0.5.0 - 2023-01-22

- Update code for dotclear 2.24

---

### Below, Historical Changelog in french

# v0.412 - 19-10-2017

- Ajout de la prise en charge du script jgallery-1.6.2
- Mise à jour vers galleria 1.5.7
- Mise à jour vers tosrus 2.5.0

# v0.410 - 04-02-2015

- Partie rédaction billet
- Intégration dans l'éditeur dcCKEditor
- Focus de la fenêtre popup
- Classement alphabétique des noms de dossiers
- Partie admin
- Ajout d'options pour réglage de la taille du script galleria et des miniatures associées (par défaut largeur 100% hauteur 400px et miniatures à 60px)
- Partie publique
- Mise à jour vers galleria 1.4.2
- Suppression de la div galleria s'il n'y a aucune image à afficher (i.e. si toutes les images sont [private])
- Remplacement du script jcarousel par le script tosrus
- Activation des script après un $(document).ready() et désactivation de la divbox pour les images en galleria

# v0.40 - 16-12-2013

- Mise à jour script DivBox v1.3 (compatible jquery 1.10.2)
- Ajout d'un #divbox_frame{-moz-box-sizing:content-box;} au divbox.css pour compatilibté avec le thème dcBootStrap
- Mise à jour script Galleria v1.2.9 (qui semble compatible jquery 1.4.2 & 1.10.2)
- Ajout d'un margin-left: auto; et margin-right: auto; au .galleria-container pour centrer les galleria
- Centrage du jcarousel (margin-left: auto; et margin-right: auto; au .jcarousel-skin-tango .jcarousel-container)

# v0.39 - 07-11-2013

- Correction des scripts d'insertion de la balise pour les modes wiki et xhtml
- Suppression du paragraphe si la galerie est directement incluse dans un paragraphe <p>::gallery::</p> (car non valide w3c)
- Modifications mineures

# v0.38 - 06-11-2013

- Correction appel à jquery pour drag'n'drop sur les images
- Remplacement de "48px" par "48" dans les tailles d'image si carousel

# v0.37 - 31-10-2013

- Correction bug showmeta avec les images sélectionnées
- Possiblité de drag'n'drop sur les images (l'ordre n'est conservé que si l'on utilise le bouton "images sélectionnées")
- Ajout d'une gestion simple des images privées
- Cela permet de cacher par défaut certaines images que l'on considère privées et de ne les afficher que pour les personnes connaissant le mot de passe
- Les images sont considérées privées si leur titre commence par '[private]'
- Si des images privées sont incluse dans une galerie un mot de passe est demandé pour afficher les images privées
- L'activation du mode image privées et le mot de passe sont configurables dans l'interface admin
- Possibilité de modifier le style dans l'interface d'administration (système proche du plugin "moreCSS")
- Modification du span en ul/li
- Alignement vertical des photos

# v0.36 - 19-10-2011

- Amélioration des traductions fr
- Vérifie la présence de la miniature et remplace par l'original si non présente
- Légère modif de la divbox : clic sur l'image pour passer à la suivante
- Amélioration du script de mise à jour auto des billets
- Ajout de l'option shometa (dans le popup d'insertion) et de la chaîne de caractère correpsondante (dans les options globales)
- Continuation de l'aide intégrée en fr et en

# v0.35 - 17-10-2011

- Amélioration des traductions fr
- Désactivation par défaut des divbox, carousel et galleria
- La liste des posts à mettre à jour dans la partie admin affiche désormais tous les types de post (post, page, ...)
- Tentative de correction du bug "LOCK TABLES"
- Correction bug dans "Ajouter les images sélectionnées" qui ne fonctionnait plus
- Début de rédaction de l'aide intégrée

# v0.34 - 14-10-2011

- Ajout de l'option "galleryinsert_enabled" pour activer ou non le plugin par blog. La valeur est à "true" par défaut.
- Ajout de l'interface d'administration
- Activation ou non du plugin
- Activation ou non des divbox / carousel / galleria
- Regénération automatique des galleries dans les billets (à utiliser avec précautions !)
- Ajout du répertoire racine à la liste (".")

# v0.33 - 10-10-2011

- Mise au norme xhtml 1.0 strict
- Inclusion dans un <span> au lieu d'un <div>
- Début de mise en place de l'affichage des métadonnées (balise "showmeta") dans le titre de l'image
- exemple ::gallery dir='toto' showmeta::
- Extension à l'ensemble des médias (images, musiques, vidéos, ...)
- Mise en place de la divbox
- Modification du comportement : la galerie est générée au moment de l'enregistrement du billet - Moins d'opérations au moment de l'affichage du billet
- Il faut ré-enregistrer les billets créés avec les versions précédentes du plugin ou lors de la mise à jour d'un répertoire ou de la mise à jour du plugin
- Possibilité d'affichage sous forme de carousel
- Ajout de l'option "carousel" (exemple ::gallery dir='toto' carousel::)
- Possibilité d'affichage sous forme de galerie
- Ajout de l'option "galleria" (exemple ::gallery dir='toto' galleria::)

# v0.32 - 28-09-2011

- Correction de quelques bugs au niveau de la gestion des paramètres sauvegardés
- Ajout du return false aux fonctions javascript pour éviter la remontée en haut de page
- Ajout de l'icône "déplier" pour les options
- Ajout des <label> pour pouvoir sélectionner les images en cliquant dessus

# v0.3 - 27-09-2011

- Modification des appellations des options au format long (square, original, ...)
- Ajout des lignes d'info dans l'interface admin
- Possibilité de sauvegarder / restaurer les options de taille et de lien.
- Ajout des boutons select all / none / invert
- Changement de l'icône

# v0.2 - 26-09-2011

- Ajout possiblité de régler la taille d'affichage (via thumb='option' ou option = [sq,t,m,s ou original])
- Ajout possiblité de régler la taille de l'image pointée par le lien (via linkto='option' ou option = [sq,t,m,s ou original ou none si pas de lien])
- La sélection des images se fait désormais par le nom du répertoire et les noms des images plutôt que par les MediaID pour éviter des problèmes lors de l'export/import de blog.
- Modification de l'interface admin pour plus de clarté et l'ajout des options

# v0.1 - 24-09-2011

- Création du plugin
- Insertion d'un répertoire via ::gallery dir='toto'::
- Insertion d'images via leurs mediaID ::gallery img='1100;1203;546'::
- Ajout de l'interface admin pour la selection du répertoire ou des images
