# language: fr

Fonctionnalité: Connexion utilisateur
  En tant qu'utilisateur
  Je veux me connecter
  Afin d'accéder à mon espace personnel

  Scénario: Connexion réussie
    Étant donné que je suis sur la page de connexion
    Lorsque je saisis "john@example.com" et "secret123"
    Et que je clique sur "Se connecter"
    Alors je devrais voir "Bienvenue John"

