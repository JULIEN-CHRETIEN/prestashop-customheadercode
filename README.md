# Custom Header Code pour PrestaShop

Un module PrestaShop qui place votre code dans le `<head>` de toutes les pages de la boutique :
balise de vérification (Search Console, Pinterest, Meta), script de mesure d'audience, bandeau
de consentement, feuille de style d'appoint. Sans toucher au thème, donc sans rien perdre à sa
prochaine mise à jour.

## Installation

1. Téléchargez `customheadercode-1.1.0.zip` dans les [versions publiées](../../releases/latest).
2. Back-office : **Modules > Gestionnaire de modules > Installer un module**, puis déposez le zip.
3. **Configurer** : collez votre code, balises comprises, puis **Enregistrer**.

En ligne de commande, depuis la racine de la boutique :

```bash
git clone https://github.com/JULIEN-CHRETIEN/prestashop-customheadercode.git modules/customheadercode
```

puis installez-le depuis le gestionnaire de modules. Le dossier doit s'appeler `customheadercode`.

## Ce que la 1.1.0 corrige

La 1.0.0 ne servait correctement qu'un script en ligne, sans commentaire. Les causes ont été
lues dans le code de PrestaShop (1.7.8 et 8.2) :

| Défaut de la 1.0.0 | Cause | En 1.1.0 |
|---|---|---|
| Un `<script src>`, une balise `<meta>` ou un `<link>` disparaissaient | `Configuration::updateValue()` sans l'option html passe la valeur par `Db::escape()`, qui applique `strip_tags()` | Le code est rangé encodé en base64 : il n'y a plus de balise à retirer |
| Un script contenant un commentaire `//` cessait de fonctionner | Avant `strip_tags()`, `Tools::nl2br()` remplace chaque retour à la ligne par `<br />`, que `strip_tags()` supprime : le script tenait sur une seule ligne et le commentaire avalait la suite | Les retours à la ligne sont conservés |
| Tout le code était enfermé dans un `<script>` | Contournement du premier défaut | Le code est servi tel qu'il a été saisi |
| Un onglet d'administration menait à une erreur | L'onglet était créé sans contrôleur | L'onglet n'existe plus, la mise à jour retire celui de la 1.0.0 |

Pourquoi pas l'option html de `updateValue()` : elle fait passer la valeur par HTMLPurifier,
activé par défaut, qui retire les balises `<meta>` et `<link>` et les attributs qu'il ne
connaît pas, comme `async`.

### Mettre à jour depuis la 1.0.0

Déposez le zip de la 1.1.0 par-dessus l'ancien. La mise à jour reprend le code enregistré tel
qu'il était servi (entouré d'un `<script>`) et retire l'ancien onglet. Ouvrez ensuite la
configuration et recollez votre code d'origine : la 1.0.0 en avait déjà perdu les balises et
les retours à la ligne au moment de l'enregistrer.

## Compatibilité

PrestaShop 1.7.0 et suivants, par le crochet `displayHeader`. Le code reste compatible PHP 5.6
pour suivre les plus anciennes boutiques en 1.7.

## Sécurité

Le code est servi tel quel, sur toutes les pages, à tous les visiteurs. Seul un employé qui a
accès à la configuration des modules peut le modifier. N'y collez que du code dont vous
connaissez la provenance : un script placé dans le `<head>` lit tout ce qui est saisi sur la
page, formulaires de commande compris.

## Auteur

Julien Chrétien, développeur web indépendant dans le Pas-de-Calais :
[julienchretien.com](https://julienchretien.com).

Licence MIT, voir [LICENSE](LICENSE).
