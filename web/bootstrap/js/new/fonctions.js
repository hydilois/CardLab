// Quitter d'une cellule à l'autre lors du replissage des notes

function next(event, ligne) {
    if (event.keyCode === 40) {
        var chaine = document.getElementById('entree' + ligne).value;
        if ((chaine !== null) && !isNaN(chaine) && (chaine >= 0) && ((chaine <= 20))) {
            var suiv = ligne + 1;
            document.getElementById('entree' + suiv).focus();
        } else {
            alert("Saisie incorrecte!\n\nLa note doit être comprise entre 0 et 20. \n\nUtilisez les point '.' pour les décimales \n\nExemples: Ecrire 12.5 au lieu de 12,5");
        }
    }
    if (event.keyCode === 38) {
        var chaine = document.getElementById('entree' + ligne).value;
        if ((chaine !== null) && !isNaN(chaine) && (chaine >= 0) && ((chaine <= 20))) {
            var prev = ligne - 1;
            document.getElementById('entree' + prev).focus();
        } else {
            alert("Saisie incorrecte!\n\nLa note doit être comprise entre 0 et 20. \n\nUtilisez les point '.' pour les décimales\n\nExemples: Ecrire 12.5 au lieu de 12,5");
        }
    }
}
