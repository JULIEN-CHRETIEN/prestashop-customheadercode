<?php
/**
 * @author    Julien Chrétien <https://julienchretien.com>
 * @license   MIT
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * De 1.0.0 à 1.1.0.
 *
 * La 1.0.0 rangeait le code sous CUSTOM_HEADER_CODE, déjà privé de ses balises et de ses
 * retours à la ligne, et l'entourait d'un <script> à l'affichage. On le reprend tel qu'il
 * était servi pour que la boutique affiche la même chose après la mise à jour, puis on
 * retire l'onglet d'administration que la 1.0.0 créait sans contrôleur derrière.
 */
function upgrade_module_1_1_0($module)
{
    $ancien = Configuration::get('CUSTOM_HEADER_CODE');
    if (is_string($ancien) && trim($ancien) !== '' && !Configuration::get(CustomHeaderCode::CLE)) {
        Configuration::updateValue(CustomHeaderCode::CLE, base64_encode('<script>' . trim($ancien) . '</script>'));
    }
    Configuration::deleteByName('CUSTOM_HEADER_CODE');

    $idOnglet = (int) Tab::getIdFromClassName('AdminCustomHeaderCode');
    if ($idOnglet) {
        $onglet = new Tab($idOnglet);
        $onglet->delete();
    }

    return $module->registerHook('displayHeader');
}
