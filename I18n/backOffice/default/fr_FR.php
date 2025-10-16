<?php

return [
    'Chronopost Tax Rule configuration'               => 'Configuration des Règles de taxes Chronopost',
    'Tax Rule'                                        => 'Règles de taxes',
    'Weight up to ... kg'                             => 'Poids jusqu\'à ... kg',
    'Untaxed Price up to ... %symbol'                 => 'Prix hors taxe jusqu\'à ... %symbol',
    'Price (%symbol)'                                 => 'Prix (%symbol)',
    'Actions'                                         => 'Actions',
    'Activate free shipping from (€) :'               => 'Activer la livraison gratuite à partir de (€) :',
    'Area : '                                         => 'Zone : ',
    'Or activate free shipping from (€) :'            => 'Ou activer la livraison gratuite à partir de (€) :',
    'Activate total free shipping '                   => 'Activer la gratuité totale des frais de port',
    'Price slices for "%mode"'                        => 'Tranches de prix pour "%mode"',
    'Advanced configuration'                          => 'Configuration avancée',
    'Delete this price slice'                         => 'Supprimer cette tranche de prix',
    'Save this price slice'                           => 'Enregister cette tranche de prix',
    'Message'                                         => 'Message',
    'manage shipping zones'                           => 'gérer les zones d\'expédition',
    'Add this price slice'                            => 'Ajouter cette tranche de prix',
    'Allowed shipping modes'                          => 'Modes d\'expédition autorisés',
    'You should first attribute shipping zones to the modules: '
                                                      => 'Vous devez d\'abord attribuer des zones d\'expédition aux modules :',
    'chronopost_home_delivery.bo.explain_price_slice' => <<<TXT
Vous pouvez créer des tranches de prix en spécifiant un poids maximal du panier et/ou un prix maximal du panier. Les tranches sont triées par poids maximal, puis par prix maximal. Si un panier correspond à plusieurs tranches, c’est la dernière tranche (selon cet ordre) qui sera appliquée.
Si vous ne spécifiez pas de poids dans une tranche, celle-ci aura la priorité sur les tranches qui en ont un.
Si vous ne spécifiez pas de prix dans une tranche, elle aura la priorité sur les autres tranches ayant le même poids.
Si vous spécifiez à la fois un poids et un prix, le panier devra avoir "un poids inférieur et un prix inférieur" pour que la tranche soit appliquée.
TXT,
    'Area not found'                                  => 'Zone de livraison non trouvée',
    'Delivery mode not found'                         => 'Mode de livraison non trouvé'
];
