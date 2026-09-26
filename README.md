# IPTV Free Countries

Petit gestionnaire PHP pour importer une playlist M3U/M3U8 **publique et autorisée**, détecter le pays de chaque chaîne et générer automatiquement une playlist par pays.

## Fonctionnement

1. Ouvrir `index.php`.
2. Importer un fichier `.m3u/.m3u8` ou saisir l’URL d’une playlist.
3. Le serveur lit `tvg-country`, puis `group-title` et le nom de la chaîne.
4. Les chaînes sont dédupliquées.
5. Les fichiers sont générés dans `countries/` : `fr.m3u`, `tn.m3u`, `lu.m3u`, etc.
6. Les chaînes dont le pays n’est pas identifié vont dans `zz.m3u`.

## Hébergement

Compatible avec PHP 8+ et un hébergement Apache/Hostinger classique.

Aucune base de données n’est nécessaire.

## Source publique d’exemple

TDTChannels publie officiellement :

- https://www.tdtchannels.com/lists/tv.m3u8
- https://www.tdtchannels.com/lists/tv.m3u

TDTChannels indique que ses listes utilisent les émissions officielles des diffuseurs. La disponibilité d’un flux peut toutefois changer ou être soumise à géoblocage.

## Important

Ce projet n’accorde aucun droit sur les contenus audiovisuels. Utilisez uniquement des playlists et flux que vous avez le droit d’accéder, d’intégrer ou de redistribuer. « Gratuit à regarder » ne signifie pas nécessairement « libre de droits ».

## Structure

```
/
├── index.php
├── import.php
├── api.php
├── lib/
│   └── playlist.php
├── countries/
├── data/
├── sources.txt
└── .htaccess
```
