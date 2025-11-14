<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Step\Given;
use Behat\Step\When;
use Behat\Step\Then;

class FeatureContext implements Context
{
    #[Given("je suis sur la page de connexion")]
    public function jeSuisSurLaPageDeConnexion(): void
    {
        echo "➡️ Je suis sur la page de connexion\n";
    }

    #[When("je saisis :email et :motDePasse")]
    public function jeSaisisMesIdentifiants($email, $motDePasse): void
    {
        echo "✏️ J'entre l'email $email et le mot de passe $motDePasse\n";
    }

    #[When("je clique sur :bouton")]
    public function jeCliqueSur($bouton): void
    {
        echo "🖱️ Je clique sur le bouton $bouton\n";
    }

    #[Then("je devrais voir :message")]
    public function jeDevraisVoir($message): void
    {
        echo "✅ Je vois le message : $message\n";
    }
}
