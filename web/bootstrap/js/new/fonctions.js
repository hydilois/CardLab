// Quitter d'une cellule à l'autre lors du replissage des notes

function next(event, ligne) {
    if (event.keyCode === 40) {
        var suiv = ligne + 1;
        document.getElementById('entree' + suiv).focus();
    }
    if (event.keyCode === 38) {
        var prev = ligne - 1;
        document.getElementById('entree' + prev).focus();
    }
}

