# Souk Kabir — Laravel Marketplace

Marketplace e-commerce Laravel 11 avec dashboards Admin/Vendeur/Client, certification vendeur, abonnements et paiement manuel par Airtel Money / Moov Money.

## Paiement manuel Mobile Money

Aucune API Airtel ou Moov n'est nécessaire pour le mode actuel.

### Abonnement vendeur
1. Le vendeur choisit un plan.
2. Il voit le nom et le numéro de transfert configurés pour Airtel Money et Moov Money.
3. Il effectue le transfert depuis son téléphone.
4. Il joint la capture de transaction et peut indiquer la référence.
5. L'administrateur vérifie la capture, le montant et le numéro.
6. L'administrateur valide ou rejette.
7. Une validation active automatiquement l'abonnement pour sa durée.

### Paiement des produits
Chaque vendeur configure ses propres numéros de transfert. Pour une commande contenant plusieurs vendeurs, le client voit le montant à payer à chaque vendeur et soumet une preuve séparée pour chaque vendeur.

L'administrateur valide chaque preuve. Lorsque toutes les parts vendeurs de la commande sont validées, la commande passe automatiquement en paiement `paid` et statut `processing`.

## Numéros de transfert vendeur

Un vendeur peut modifier son nom de titulaire et ses numéros Airtel Money / Moov Money depuis :

`/vendor/payment-settings`

Les informations utilisées au moment d'une soumission sont copiées dans la transaction afin de conserver une trace même si le vendeur change ensuite son numéro.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Comptes de démonstration

Admin:
- `admin@soukkabir.com`
- `Admin@12345`

Vendeur:
- `vendeur@soukkabir.com`
- `Vendor@12345`

## Production

- `APP_ENV=production`
- `APP_DEBUG=false`
- configurer MySQL dans `.env`
- exécuter `php artisan migrate --force`
- exécuter `php artisan storage:link`
- ne jamais committer `.env`

## API Mobile Money

Le projet ne dépend plus d'Airtel Money API ou Moov Money API pour le flux de paiement manuel. Une intégration API pourra être ajoutée plus tard comme nouveau gateway sans supprimer le mode manuel.

## Marketplace multi-vendeurs

Le panier est unique pour le client, mais au moment de la commande Laravel regroupe automatiquement les articles par vendeur.

- Une commande principale : `SK-XXXXXXXX`
- Une sous-commande par vendeur : `SK-XXXXXXXX-V{vendor_id}`
- Chaque sous-commande possède son propre montant et son propre statut de paiement.
- Pour le paiement manuel Airtel Money / Moov Money, le client envoie une preuve pour chaque sous-commande vendeur.
- L'administrateur valide chaque paiement séparément.
- La commande principale passe à `paid` uniquement lorsque toutes les sous-commandes sont payées.
- Le numéro de transfert utilisé est copié dans la preuve de paiement afin de conserver l'historique même si le vendeur change ensuite son numéro.

### Exemple

Panier client : 140 000 FCFA

- Vendeur A : 35 000 FCFA
- Vendeur B : 80 000 FCFA
- Vendeur C : 25 000 FCFA

Le client paie chaque vendeur séparément. Le total global reste 140 000 FCFA dans la commande principale.
