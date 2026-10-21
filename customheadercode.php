<?php
/**
 * Custom Header Code : place votre code (balises de vérification, mesure d'audience,
 * bandeau de consentement) dans le <head> de toutes les pages de la boutique.
 *
 * @author    Julien Chrétien <https://julienchretien.com>
 * @license   MIT
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomHeaderCode extends Module
{
    /**
     * Le code est rangé encodé en base64.
     *
     * Configuration::updateValue() fait passer la valeur par Db::escape(), qui applique
     * strip_tags(Tools::nl2br()) sans l'option html : les balises disparaissent, et les
     * retours à la ligne aussi. Avec l'option html, la valeur traverse HTMLPurifier quand
     * il est activé (réglage par défaut), qui ne rend intacts ni un <script async>, ni une
     * balise <meta>, ni un <link>. Encodé, le code ne contient plus rien à retirer.
     */
    const CLE = 'CUSTOM_HEADER_CODE_B64';

    public function __construct()
    {
        $this->name = 'customheadercode';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'Julien Chrétien';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Custom Header Code');
        $this->description = $this->l('Place votre code (balises de vérification, mesure d\'audience, consentement) dans le <head> de toutes les pages.');
        $this->confirmUninstall = $this->l('Le code enregistré sera supprimé. Continuer ?');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName(self::CLE);

        return parent::uninstall();
    }

    public function hookDisplayHeader($params)
    {
        return self::lire();
    }

    public function getContent()
    {
        $sortie = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $code = trim((string) Tools::getValue('CUSTOM_HEADER_CODE'));
            Configuration::updateValue(self::CLE, base64_encode($code));
            $sortie .= $this->displayConfirmation($this->l('Code enregistré.'));
        }

        return $sortie . $this->renderForm();
    }

    /**
     * Le code tel qu'il a été saisi, prêt à servir.
     *
     * @return string
     */
    public static function lire()
    {
        $valeur = Configuration::get(self::CLE);
        if (!is_string($valeur) || $valeur === '') {
            return '';
        }
        $code = base64_decode($valeur, true);

        return $code === false ? '' : $code;
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => ['CUSTOM_HEADER_CODE' => self::lire()],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Code à placer dans le <head>'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Code'),
                        'name' => 'CUSTOM_HEADER_CODE',
                        'cols' => 60,
                        'rows' => 14,
                        'desc' => $this->l('Collez le code complet, balises comprises (<script>, <meta>, <link>, <style>). Il est servi tel quel sur toutes les pages : n\'y mettez que du code dont vous connaissez la provenance.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                ],
            ],
        ];
    }
}
