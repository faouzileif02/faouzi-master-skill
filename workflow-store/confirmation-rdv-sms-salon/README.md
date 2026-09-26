# Confirmation RDV automatique par SMS pour salon de coiffure

> **Statut : BETA — validation technique en cours**

Automatisez la confirmation des rendez-vous clients par SMS avec n8n, Google Sheets, Twilio et Gmail.

## Problème résolu

Les salons perdent du temps à confirmer manuellement leurs rendez-vous et peuvent subir des créneaux perdus lorsque les clients ne préviennent pas en cas d'annulation.

Ce workflow vise à :

- détecter les rendez-vous du lendemain ;
- envoyer automatiquement un SMS au client ;
- enregistrer le statut de la confirmation ;
- traiter les réponses OUI / NON ;
- prévenir le gérant lorsqu'un créneau est libéré ;
- signaler les clients sans numéro ou sans réponse.

## Pour qui ?

- salons de coiffure ;
- barbiers ;
- instituts de beauté ;
- indépendants utilisant Google Sheets pour gérer leurs rendez-vous.

## Technologies

- n8n
- Google Sheets
- Twilio
- Gmail

## Fonctionnement prévu

1. Lecture quotidienne des rendez-vous du lendemain.
2. Vérification du numéro de téléphone.
3. Envoi du SMS de confirmation.
4. Enregistrement de l'état `SMS_ENVOYE`.
5. Réception des réponses SMS.
6. Association du numéro entrant avec le bon rendez-vous.
7. Mise à jour en `CONFIRME` ou `ANNULE`.
8. Notification du gérant lorsqu'un créneau devient disponible.

## Prérequis

- une instance n8n ;
- un compte Google ;
- Google Sheets ;
- Gmail ;
- un compte Twilio avec numéro SMS actif ;
- un webhook public HTTPS pour la réception des SMS.

## Important

La version commerciale n'est pas encore publiée dans ce dépôt.

La structure actuelle doit encore être séparée proprement entre :
- le workflow d'envoi des confirmations ;
- le workflow de réception des réponses Twilio.

Le fichier JSON complet reste privé pendant cette phase de validation.

## Version commerciale prévue

Le pack final pourra comprendre :

- workflow n8n prêt à importer ;
- modèle Google Sheets ;
- guide d'installation ;
- configuration Twilio ;
- configuration des webhooks ;
- personnalisation du message SMS ;
- support d'installation en option.

## Licence / utilisation

Cette page présente le produit. Elle ne fournit pas le workflow commercial complet ni les identifiants/API nécessaires à son fonctionnement.

---

Développé comme solution d'automatisation pour les professionnels de la coiffure et de la beauté.
