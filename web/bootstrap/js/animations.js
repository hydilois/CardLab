function detail(id) {
    if ($('.pubS_' + id).hasClass('expd')) {
        $('.pubS_' + id).hide("slow");
        $('.pubS_' + id).removeClass('expd');
        //$('.page_pub').addClass('col-md-offset-3');
        //changeGlyphicon(1, id);
    } else {
        $('.expd').hide("slow");
        $('.expd').removeClass('expd');
        $('.pubS_' + id).show("slow");
        $('.pubS_' + id).addClass('expd');
        //changeGlyphicon(0, id);
        //$('.page_pub').removeClass('col-md-offset-3');
    }
}

function listeProjets() {
    $('.listeProjets').removeClass('hide');
}


moisX = ["", "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"];
JourM = ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"];

var fermable_microcal = true;
var select_old = null;

var startWeek = 0;//debut de la semaine 0=dim,1=lun,...
var jourPause = {0: true, 6: true}; //jour de pause de la semaine
var jourFeriee = {"1-1": "jour an", "1-5": "fête du travail", "8-5": "armistice", "14-7": "fête nationale", "15-8": "ascencion", "1-11": "armistice", "11-11": "toussain", "25-12": "noel"};

//structure la date 
function strucDate(dateX)
{
    return {"pos": dateX.getDay(), "jour": dateX.getDate(), "mois": dateX.getMonth() + 1, "annee": dateX.getFullYear()};
}

var dateS = strucDate(new Date());//date Selectionné
var dnow = strucDate(new Date());//date actuelle

//retourne le ième jour du 1er du mois
function premJourMois(mois, annee)
{
    return (new Date(annee, mois - 1, 1).getDay());
}
//retourne le jour max du mois
function JmaxMois(mois, annee)
{
    return (new Date(annee, mois, 0).getDate());
}

/* Test une date si elle est correct...spécial killer*/
function testTypeDate(dateEntree)
{
    tst = false;
    try
    {
        rc = dateEntree.split("/");
        nd = new Date(rc[2], (rc[1] - 1), rc[0]);
        tst = (rc[2] > 1800 && rc[2] < 2200 && rc[2] == nd.getFullYear() && rc[1] == (nd.getMonth() + 1) && rc[0] == nd.getDate());
    } catch (e) {
    }
    return tst;
}

//selection de la zone avec la souris
function choix(koi, code)
{
    if (code)
    {
        select_old = koi.style.background;
        koi.style.background = '#c0c0FF';
    }
    else
    {
        koi.style.background = select_old;
    }
}

function testTravail(oldX, xx, jj, mm, aa)
{
    styleX = "font-family:Tahoma;font-size:10px;text-align:center;";
    styleX += (oldX) ? "" : "color:#e0e0e0;";
    styleX += "cursor:hand;border-right:1px #e0e0e0 solid;border-bottom:1px #e0e0e0 solid;";
    if (jourPause[xx] || jourFeriee[jj + "-" + mm] != null)
        styleX += "background:#f0f0f0;";
    if (jj == dnow.jour && mm == dnow.mois && aa == dnow.annee)
        styleX += "border:2px red solid;";
    return styleX;
}

//test si année bissextile
function bissextile(annee) {
    return (annee % 4 == 0 && annee % 100 != 0 || annee % 400 == 0);
}

//Retourne le nombre de jour depuis le 1er janvier (num de semaine)
function nbJAnnee(dateX) {
    var nb_mois = [, 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    j = dateX.jour;
    m = dateX.mois;
    a = dateX.annee;
    nb = nb_mois[m] + j - 1;
    if (bissextile(a) && m > 2)
        nb++;
    return nb;
}

//affiche le calendrier
function view_microcal(actif, ki, source, mxS, axS)
{
    if (actif)
    {
//decalage du mois su on clique sur -/+
        if (mxS != -1)
        {
            clearTimeout(cc);
            ki.focus();
            fermable_microcal = true;
            dateS.mois = mxS;
            dateS.annee = axS;
            if (dateS.mois < 1) {
                dateS.annee--;
                dateS.mois += 12;
            }
            if (dateS.mois > 12) {
                dateS.annee++;
                dateS.mois -= 12;
            }
        }
//init
        Dstart = (premJourMois(dateS.mois, dateS.annee) + 7 - startWeek) % 7;
        jmaxi = JmaxMois(dateS.mois, dateS.annee);
        jmaxiAvant = JmaxMois((dateS.mois - 1), dateS.annee);
//si on veux ajouter le numero de la semaine ...
//idxWeek=parseInt(nbJAnnee(strucDate(new Date(dateS.mois+'-01-'+dateS.annee)))/7,10)+1;

        ymaxi = parseInt((jmaxi + Dstart + 1) / 7, 10);

//generation du tableau
//--entête
        htm = "<table class='table table-bordered'><tr style='font-size:10px;font-family:Tahoma;text-align:center;'>";
        htm += "<td class='alert-warning' style='cursor:hand;' onclick=\"view_microcal(true," + ki.id + "," + source.id + "," + (dateS.mois - 1) + "," + dateS.annee + ");\">-</td>";
        htm += "<td class='alert-success' colspan='5'> <b> " + moisX[dateS.mois] + "</b>&nbsp;" + dateS.annee + "</td>";
        htm += "<td class='alert-warning'  style='cursor:hand;' onclick=\"view_microcal(true," + ki.id + "," + source.id + "," + (dateS.mois + 1) + "," + dateS.annee + ")\">+</td></tr>";
//--corps
        htm += "<tr>";
//affichage des jours DLMMJVS
        for (x = 0; x < 7; x++)
            htm += "<td  style='font-size:10px;font-family:Tahoma;' class='alert-success'><b>" + JourM[(x + startWeek) % 7] + "</b></td>";
        htm += "</tr>"

//------------------------
        for (y = 0; y <= ymaxi; y++)
        {
            htm += "<tr>";
            for (x = 0; x < 7; x++)
            {
                idxP = y * 7 + x - Dstart + 1;   //numero du jour
                aa = dateS.annee;
                xx = (x + startWeek) % 7;
//jour du mois précedent
                if (idxP <= 0)
                {
                    jj = idxP + jmaxiAvant;
                    mm = dateS.mois - 1;
                    if (mm == 0)
                    {
                        mm = 12;
                        aa--;
                    }
                    htm += "<td style='" + testTravail(false, xx, jj, mm, aa) + "' onmouseover='choix(this,true)' onmouseout='choix(this,false)'  onclick=\"" + (ki.id) + ".value='" + ((jj < 10) ? "0" : "") + jj + "/" + ((mm < 10) ? "0" : "") + mm + "/" + aa + "';" + (ki.id) + ".style.color='black';\">" + jj + "</td>";
                }
                else if (idxP > jmaxi) //jour du mois suivant
                {
                    jj = idxP - jmaxi;
                    mm = dateS.mois + 1;
                    if (mm == 13)
                    {
                        mm = 1;
                        aa++;
                    }

                    htm += "<td style='" + testTravail(false, xx, jj, mm, aa) + "' onmouseover='choix(this,true)' onmouseout='choix(this,false)'  onclick=\"" + (ki.id) + ".value='" + ((jj < 10) ? "0" : "") + jj + "/" + ((mm < 10) ? "0" : "") + mm + "/" + aa + "';" + (ki.id) + ".style.color='black';\">" + jj + "</td>";
                }
                else //jour du mois en cours
                {
                    jj = idxP;
                    mm = dateS.mois;
                    htm += "<td style='" + testTravail(true, xx, jj, mm, aa) + "' onmouseover='choix(this,true)' onmouseout='choix(this,false)'  onclick=\"" + (ki.id) + ".value='" + ((jj < 10) ? "0" : "") + jj + "/" + ((mm < 10) ? "0" : "") + mm + "/" + aa + "';" + (ki.id) + ".style.color='black';\">" + jj + "</td>";
                }
            }
            htm += "</tr>"
        }//-------------------------
        htm += "</table>"
//affiche le tableau
        source.innerHTML = htm;
        source.style.visibility = "";
    } else
    {
//ferme le calendrier
        if (fermable_microcal)
            cc = setTimeout(source.id + ".style.visibility='hidden'", 500);
    }
}
view_microcal(true, dateDebut, microcal, -1, 0);
