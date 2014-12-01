<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_articleur_traiter_dist() {
    //Traitement du formulaire.
    include_spip('action/editer_article');

    $where = array();

    // Exclure la racine si demandé.
    if (_request('exclure_racine'))
        $where[] = 'id_parent != 0';

    // Récupération de toutes les rubriques du site
    $rubriques = sql_allfetsel(
        'id_rubrique, titre, lang, id_trad',
        'spip_rubriques',
        $where
    );

    // Lancer la boucle
    foreach($rubriques as $rubrique) {

        // Quelques varibles
        $id_rubrique = $rubrique['id_rubrique'];
        $titre = supprimer_numero($rubrique['titre']);
        $lang = $rubrique['lang'];
        $id_trad = $rubrique['id_trad'];

        // On teste, est-ce qu'il y a déjà un article ?
        // On ne tient pas compte des articles à la poubelle
        $test_article = articleur_compter_article_rubrique($id_rubrique);

        // Pas d'article, on le créer
        if ($test_article <= 0) {

            // On va passer l'id_rubrique dans le request
            // Sinon on ne peux pas ajouter un article en utilisant le plugin
            // Traduction d'article autrement (merci rainer)
            set_request('id_rubrique', $id_rubrique);

            // Créer l'article
            $id_article = article_inserer($id_rubrique);

            // On va essayer de trouver automatiquement la référence de traduction
            // On compte le nombre d'article dans la rubrique de référence
            $nb_in_trad = articleur_compter_article_rubrique($id_trad);

            // Si il n'y a qu'un seul article, on peux dire que c'est la traduction
            if ($nb_in_trad == 1) {
                $id_trad_article = sql_getfetsel('id_article', 'spip_articles', 'id_rubrique='.intval($id_trad));

                // Du coup il devient une référence de tradution !
                article_modifier(
                    $id_trad_article,
                    array('id_trad' => $id_trad_article)
                );
            }
            // Sinon, bah on peux pas deviner
            else
                $id_trad_article = 0;

            $erreurs = article_modifier(
                $id_article,
                array(
                    'titre' => $titre,
                    'texte' => _request('texte_article'),
                    'statut' => 'publie',
                    'lang' => $lang, // L'article va prendre la langue de la rubrique
                    'id_trad' => $id_trad_article
                )
            );


            if ($erreurs)
                return array('message_erreur' => $erreurs);
        }
    }

    // Donnée de retour.
    return array(
        'editable' => true,
        'message_ok' => 'Article créer !'
    );
}