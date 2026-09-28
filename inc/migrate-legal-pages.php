<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Migration one-shot : remplit les pages légales (mentions légales, confidentialité)
 * si elles sont vides ou absentes (la CI ne déploie que le code, pas le contenu).
 */

const MKWVS_LEGAL_PAGE_TEMPLATE = 'templates/page-legale.php';
const MKWVS_LEGAL_EMAIL = 'lebusmagique.lille@gmail.com';

add_action('init', 'mkwvs_migrate_legal_pages');

function mkwvs_migrate_legal_pages(): void
{
    if ((int) get_option('mkwvs_legal_pages_migrated', 0) >= 6) {
        return;
    }

    $ok = mkwvs_migrate_fill_legal_page(
        'mentions-legales',
        'Mentions légales',
        mkwvs_legal_protect_email(mkwvs_legal_page_content_mentions())
    );

    $ok = mkwvs_migrate_fill_legal_page(
        'confidentialite',
        'Politique de confidentialité',
        mkwvs_legal_protect_email(mkwvs_legal_page_content_confidentialite())
    ) && $ok;

    if ($ok) {
        update_option('mkwvs_legal_pages_migrated', 6);
    }
}

function mkwvs_migrate_fill_legal_page(string $slug, string $title, string $content): bool
{
    $page = get_page_by_path($slug);

    if (!$page instanceof WP_Post) {
        $id = wp_insert_post([
            'post_type'     => 'page',
            'post_status'   => 'publish',
            'post_title'    => $title,
            'post_name'     => $slug,
            'post_content'  => $content,
            'page_template' => MKWVS_LEGAL_PAGE_TEMPLATE,
        ], true);

        return !is_wp_error($id) && (int) $id > 0;
    }

    update_post_meta($page->ID, '_wp_page_template', MKWVS_LEGAL_PAGE_TEMPLATE);

    if (trim(strip_tags($page->post_content)) !== '' && !mkwvs_legal_page_is_outdated($page->post_content)) {
        return true;
    }

    $updated = wp_update_post([
        'ID'           => $page->ID,
        'post_content' => $content,
        'post_status'  => 'publish',
    ], true);

    return !is_wp_error($updated);
}

/**
 * Encode l'adresse en entités HTML : elle reste cliquable et lisible, mais n'est
 * plus servie en clair aux moissonneurs d'adresses.
 */
function mkwvs_legal_protect_email(string $content): string
{
    if (!function_exists('antispambot')) {
        return $content;
    }

    return str_replace(MKWVS_LEGAL_EMAIL, antispambot(MKWVS_LEGAL_EMAIL), $content);
}

function mkwvs_legal_page_is_outdated(string $content): bool
{
    foreach (['À COMPLÉTER', 'Le site est hébergé par OVH SAS', 'avec Matomo', 'uReserve', 'prestataires de réservation', MKWVS_LEGAL_EMAIL] as $marker) {
        if (str_contains($content, $marker)) {
            return true;
        }
    }

    return false;
}

function mkwvs_legal_page_content_mentions(): string
{
    return <<<'HTML'
<!-- wp:paragraph -->
<p>Conformément aux articles 6-III et 19 de la loi n° 2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique, les présentes mentions légales précisent l'identité des intervenants du site lebusmagiquelille.fr.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Éditeur du site</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Le site lebusmagiquelille.fr est édité par Le Bus Magique, association déclarée régie par la loi du 1er juillet 1901.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"legal-identity"} -->
<ul class="wp-block-list legal-identity">
<!-- wp:list-item -->
<li>Siège social : péniche Le Bus Magique, avenue Cuvier, 59800 Lille</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Numéro RNA : W595031094</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>SIREN : 840 181 259</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>SIRET du siège : 840 181 259 00036</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Numéro de TVA intracommunautaire : FR 50 840181259</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Code APE : 56.10A (restauration traditionnelle)</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Téléphone : 03 74 09 78 81</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Adresse e-mail : lebusmagique.lille@gmail.com</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>Direction de la publication</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La directrice de la publication est Lucie Bailleul, présidente de l'association Le Bus Magique et représentante légale de celle-ci.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Hébergement</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Le site est hébergé par Symfolidity (SEOPress.host), SASU au capital de 1 000 €, 108 rue Bayard, 59000 Lille, France, RCS Lille Métropole 912 206 372. Contact : contact@seopress.host.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Le serveur est fourni par OVH SAS, 2 rue Kellermann, 59100 Roubaix, France. Téléphone : 1007.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Conception et réalisation</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Direction artistique : Atelier Jugeote. Développement : Makewaves.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Propriété intellectuelle</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>L'ensemble des éléments composant ce site, à savoir les textes, photographies, illustrations, logos, vidéos, éléments graphiques et code source, est protégé par le droit d'auteur et reste la propriété de l'association Le Bus Magique ou de ses partenaires et prestataires.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Toute reproduction, représentation, adaptation ou exploitation, totale ou partielle, sur quelque support que ce soit, est interdite sans autorisation écrite préalable de l'association. Une demande d'utilisation peut être adressée à lebusmagique.lille@gmail.com.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Liens hypertextes</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Le site peut proposer des liens vers des sites tiers, notamment ceux de nos partenaires, de notre prestataire de réservation et de notre prestataire de don. L'association n'exerce aucun contrôle sur ces sites et décline toute responsabilité quant à leur contenu ou à l'usage qui pourrait en être fait.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Responsabilité</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>L'association met tout en œuvre pour proposer des informations exactes et tenues à jour, en particulier concernant la programmation, les horaires et les tarifs. Ces informations sont néanmoins susceptibles d'évoluer et sont fournies à titre indicatif. Elles ne constituent pas un engagement contractuel.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Si vous constatez une erreur ou une information obsolète, vous pouvez nous l'indiquer à lebusmagique.lille@gmail.com.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Données personnelles et cookies</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Le traitement des données personnelles collectées via ce site, ainsi que l'usage des cookies, sont détaillés dans notre <a href="/confidentialite/">politique de confidentialité</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Droit applicable</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Les présentes mentions légales sont soumises au droit français. En cas de litige, et à défaut de résolution amiable, les tribunaux français sont seuls compétents.</p>
<!-- /wp:paragraph -->
HTML;
}

function mkwvs_legal_page_content_confidentialite(): string
{
    return <<<'HTML'
<!-- wp:paragraph -->
<p>L'association Le Bus Magique attache de l'importance à la protection de votre vie privée. Cette page explique quelles données personnelles sont collectées sur lebusmagiquelille.fr, pourquoi, combien de temps elles sont conservées et comment exercer vos droits.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Responsable du traitement</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Le responsable du traitement est l'association Le Bus Magique, association loi 1901, dont le siège est situé péniche Le Bus Magique, avenue Cuvier, 59800 Lille. Pour toute question relative à vos données : lebusmagique.lille@gmail.com. Les coordonnées complètes figurent dans nos <a href="/mentions-legales/">mentions légales</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Données collectées et finalités</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3} -->
<h3>Formulaire de contact</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nous collectons les informations que vous saisissez dans le formulaire, notamment vos nom, prénom, adresse e-mail et le contenu de votre message. Ces données servent uniquement à traiter votre demande et à y répondre. Base légale : notre intérêt légitime à répondre aux personnes qui nous sollicitent.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>Newsletter</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lors de votre inscription, nous collectons votre adresse e-mail afin de vous envoyer notre lettre d'information hebdomadaire. Base légale : votre consentement. Vous pouvez le retirer à tout moment grâce au lien de désabonnement présent dans chaque envoi, ou en nous écrivant.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>Demandes de privatisation et de réservation d'espace</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nous collectons vos nom, prénom, adresse e-mail, numéro de téléphone, le nom de votre organisation le cas échéant, la date et le créneau souhaités, le nombre de personnes attendues, les options de restauration retenues et les précisions que vous nous transmettez. Ces données permettent d'étudier votre demande, d'établir un devis et d'organiser votre événement. Base légale : l'exécution de mesures précontractuelles puis du contrat.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>Réservations, adhésions et dons</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Les réservations en ligne sont opérées par Uniiti et les dons par HelloAsso. Les données que vous saisissez dans ces outils sont collectées directement par ces prestataires, selon leurs propres politiques de confidentialité. Nous en recevons ce qui nous est nécessaire pour assurer votre accueil et le suivi de votre don.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Les adhésions, en revanche, ne passent par aucun prestataire extérieur : le formulaire d'adhésion est hébergé sur notre propre site et les données que vous y saisissez sont traitées directement par l'association.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Destinataires des données</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vos données sont accessibles aux membres habilités de l'équipe et du bureau de l'association. Elles sont également traitées par nos prestataires techniques, qui agissent en qualité de sous-traitants et uniquement selon nos instructions :</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li>Symfolidity (SEOPress.host), pour l'hébergement du site et des données qu'il contient, sur un serveur situé en France</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Brevo, pour l'envoi des e-mails de la newsletter et des e-mails de suivi de vos demandes</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Uniiti, pour la gestion des réservations en ligne</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>HelloAsso, pour la gestion des dons</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Les adhésions sont gérées avec notre propre outil, hébergé avec le reste du site : aucun prestataire tiers n'y a accès.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Nos prestataires d'hébergement et d'envoi d'e-mails sont établis dans l'Union européenne. Vos données ne sont ni vendues, ni louées, ni cédées à des tiers à des fins commerciales.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Durées de conservation</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li>Demandes envoyées via le formulaire de contact : 3 ans à compter de notre dernier échange</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Inscription à la newsletter : jusqu'à votre désinscription</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Demandes de privatisation : 3 ans à compter du dernier échange, et jusqu'à 10 ans pour les pièces comptables associées à un événement réalisé, conformément à nos obligations légales</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Statistiques de fréquentation du site : données anonymes, sans cookie ni adresse IP conservée (voir « Mesure d'audience »)</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>Mesure d'audience</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nous mesurons la fréquentation du site avec Umami, un outil de mesure d'audience sans cookie, installé sur un serveur de notre prestataire d'hébergement situé en France. Il ne conserve ni votre adresse IP ni aucun identifiant permettant de vous reconnaître d'une visite à l'autre. Les statistiques produites ne sont transmises à aucune régie publicitaire. Elles nous servent uniquement à comprendre quelles pages sont consultées afin d'améliorer le site.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Cookies et ressources tierces</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Le site dépose des cookies strictement nécessaires à son fonctionnement, notamment pour la sécurité des formulaires et, le cas échéant, pour votre session de connexion à l'espace d'administration.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Certaines pages intègrent des contenus et des services fournis par des tiers : le module de réservation Uniiti, le module de don HelloAsso, ainsi que des vidéos hébergées sur des plateformes externes. Lorsque ces contenus se chargent, le prestataire concerné reçoit votre adresse IP et peut déposer ses propres cookies, régis par sa politique de confidentialité. Le formulaire d'adhésion, lui, est hébergé sur notre propre site et ne fait appel à aucun service tiers. Vous pouvez à tout moment configurer votre navigateur pour refuser ou supprimer les cookies.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Vos droits</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Conformément au règlement général sur la protection des données et à la loi Informatique et Libertés, vous disposez d'un droit d'accès, de rectification, d'effacement, de limitation et d'opposition au traitement de vos données, ainsi que d'un droit à la portabilité et du droit de définir des directives relatives à leur sort après votre décès.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Pour exercer ces droits, écrivez-nous à lebusmagique.lille@gmail.com ou par courrier à l'adresse du siège de l'association. Si vous estimez, après nous avoir contactés, que vos droits ne sont pas respectés, vous pouvez adresser une réclamation à la CNIL sur <a href="https://www.cnil.fr/fr/plaintes" target="_blank" rel="noopener">www.cnil.fr</a>.</p>
<!-- /wp:paragraph -->
HTML;
}
