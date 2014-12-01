<?php
/**
 * Fonctions utiles au plugin articleur
 *
 * @plugin     articleur
 * @copyright  2014
 * @author     vertige (Didier)
 * @licence    GNU/GPL
 * @package    SPIP\Articleur\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function articleur_compter_article_rubrique($id_rubrique) {
    $nb = sql_countsel(
        'spip_articles',
        array(
            'id_rubrique='.intval($id_rubrique),
            'statut!='.sql_quote('poubelle')
        )
    );

    return $nb;
}