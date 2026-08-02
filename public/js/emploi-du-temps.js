(function() {
    var config = window.emploiDuTempsConfig || {};
    var slug = config.slug;
    var baseUrl = '/' + slug + '/emploi-du-temps';
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    csrfToken = csrfToken ? csrfToken.content : '';
    var jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

    var ecoleInfo = config.ecole || {};

    var niveauSelectionne = null;
    var classeSelectionnee = null;
    var anneeSelectionnee = null;
    var matieresCache = [];
    var professeursCache = [];
    var modalCreneau = null;
    var dernierCreneaux = [];

    function toast(message, type) {
        type = type || 'success';
        Toastify({
            text: message,
            duration: 3500,
            gravity: 'top',
            position: 'right',
            style: { background: type === 'success' ? '#2e7d32' : (type === 'error' ? '#c62828' : '#1565c0') }
        }).showToast();
    }

    function loader(show, title) {
        title = title || 'Traitement en cours...';
        if (show) Swal.fire({ title: title, allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
        else Swal.close();
    }

    function apiFetch(url, options) {
        options = options || {};
        options.headers = Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, options.headers || {});
        return fetch(url, options).then(function(res) {
            return res.json().catch(function() { return {}; }).then(function(data) {
                if (!res.ok) throw new Error(data.message || 'Une erreur est survenue');
                return data;
            });
        });
    }

    function formatHeure(h) {
        return h ? h.substring(0, 5) : '';
    }

    document.getElementById('niveauSelect').addEventListener('change', function() {
        var niveauId = this.value;
        var classeSelect = document.getElementById('classeSelect');
        classeSelect.innerHTML = '<option value="">Selectionner une classe</option>';

        if (!niveauId) return;

        classeSelect.innerHTML = '<option>Chargement...</option>';
        apiFetch(baseUrl + '/classes/' + niveauId).then(function(data) {
            var html = '<option value="">Selectionner une classe</option>';
            data.data.forEach(function(c) {
                html += '<option value="' + c.i_classe_id + '">' + c.v_nom_classe + '</option>';
            });
            classeSelect.innerHTML = html;
        }).catch(function(e) {
            classeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            toast(e.message, 'error');
        });
    });

    document.getElementById('btnAfficher').addEventListener('click', function() {
        var niveauId = document.getElementById('niveauSelect').value;
        var classeId = document.getElementById('classeSelect').value;
        var annee = document.getElementById('anneescolaireSelect').value;

        if (!niveauId || !classeId || !annee) {
            toast('Annee scolaire, niveau et classe sont obligatoires.', 'error');
            return;
        }

        niveauSelectionne = niveauId;
        classeSelectionnee = classeId;
        anneeSelectionnee = annee;

        var classeSelect = document.getElementById('classeSelect');
        var nomClasse = classeSelect.selectedOptions[0].textContent;
        document.getElementById('classeNomAffiche').textContent = nomClasse;

        loader(true, "Chargement de l'emploi du temps...");

        Promise.all([
            apiFetch(baseUrl + '/matieres?classe_id=' + classeId + '&annee_scolaire=' + encodeURIComponent(annee)),
            apiFetch(baseUrl + '/professeurs')
        ]).then(function(results) {
            matieresCache = results[0].data;
            professeursCache = results[1].data;
            return chargerEtAfficherCreneaux();
        }).then(function() {
            loader(false);
            document.getElementById('zoneEmploiDuTemps').classList.remove('d-none');
        }).catch(function(e) {
            loader(false);
            toast(e.message, 'error');
        });
    });

    function chargerEtAfficherCreneaux() {
        return apiFetch(baseUrl + '/creneaux?niveau_id=' + niveauSelectionne + '&classe_id=' + classeSelectionnee + '&annee_scolaire=' + encodeURIComponent(anneeSelectionnee))
            .then(function(data) {
                dernierCreneaux = data.data;
                renderGrid(dernierCreneaux);
            });
    }

    function renderGrid(creneaux) {
        var grid = document.getElementById('timetableGrid');
        var gridHtml = '';

        jours.forEach(function(jour) {
            var creneauxJour = creneaux.filter(function(c) { return c.jour === jour; });
            var cardsHtml = '';

            if (creneauxJour.length) {
                creneauxJour.forEach(function(c) {
                    cardsHtml += '<div class="creneau-card" data-id="' + c.id + '">';
                    cardsHtml += '<div class="creneau-actions">';
                    cardsHtml += '<iconify-icon icon="mdi:pencil-outline" class="edit-icon" title="Modifier"></iconify-icon>';
                    cardsHtml += '<iconify-icon icon="mdi:trash-can-outline" class="delete-icon" title="Supprimer"></iconify-icon>';
                    cardsHtml += '</div>';
                    cardsHtml += '<div class="heure">' + formatHeure(c.heure_debut) + ' - ' + formatHeure(c.heure_fin) + '</div>';
                    cardsHtml += '<div class="matiere">' + c.matiere_nom + '</div>';
                    if (c.professeur_nom) {
                        cardsHtml += '<div class="prof"><iconify-icon icon="mdi:account-outline"></iconify-icon> ' + (c.professeur_prenom || '') + ' ' + c.professeur_nom + '</div>';
                    }
                    if (c.salle) {
                        cardsHtml += '<div class="salle"><iconify-icon icon="mdi:door"></iconify-icon> ' + c.salle + '</div>';
                    }
                    cardsHtml += '</div>';
                });
            } else {
                cardsHtml = '<div class="empty-jour">Aucun cours</div>';
            }

            gridHtml += '<div class="jour-colonne"><div class="jour-header">' + jour + '</div><div class="jour-body">' + cardsHtml + '</div></div>';
        });

        grid.innerHTML = gridHtml;

        grid.querySelectorAll('.edit-icon').forEach(function(icon) {
            icon.addEventListener('click', function() {
                var id = this.closest('.creneau-card').dataset.id;
                var creneau = creneaux.find(function(c) { return String(c.id) === String(id); });
                ouvrirModal(creneau);
            });
        });

        grid.querySelectorAll('.delete-icon').forEach(function(icon) {
            icon.addEventListener('click', function() {
                var id = this.closest('.creneau-card').dataset.id;
                Swal.fire({
                    title: 'Supprimer ce creneau ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then(function(confirm) {
                    if (!confirm.isConfirmed) return;

                    loader(true, 'Suppression...');
                    apiFetch(baseUrl + '/' + id, { method: 'DELETE' }).then(function(data) {
                        toast(data.message, 'success');
                        return chargerEtAfficherCreneaux();
                    }).catch(function(e) {
                        toast(e.message, 'error');
                    }).finally(function() {
                        loader(false);
                    });
                });
            });
        });
    }

    function remplirSelectsModal() {
        var matiereSelect = document.getElementById('creneauMatiere');
        var html = '<option value="">Selectionner une matiere</option>';
        matieresCache.forEach(function(m) { html += '<option value="' + m.id + '">' + m.nom + '</option>'; });
        matiereSelect.innerHTML = html;

        var profSelect = document.getElementById('creneauProfesseur');
        var htmlProf = '<option value="">Aucun</option>';
        professeursCache.forEach(function(p) { htmlProf += '<option value="' + p.id + '">' + p.prenom + ' ' + p.nom + '</option>'; });
        profSelect.innerHTML = htmlProf;
    }

    function ouvrirModal(creneau) {
        remplirSelectsModal();

        if (creneau) {
            document.getElementById('modalCreneauTitre').textContent = 'Modifier le creneau';
            document.getElementById('creneauId').value = creneau.id;
            document.getElementById('creneauJour').value = creneau.jour;
            document.getElementById('creneauHeureDebut').value = formatHeure(creneau.heure_debut);
            document.getElementById('creneauHeureFin').value = formatHeure(creneau.heure_fin);
            document.getElementById('creneauMatiere').value = creneau.matiere_id;
            document.getElementById('creneauProfesseur').value = creneau.professeur_id || '';
            document.getElementById('creneauSalle').value = creneau.salle || '';
        } else {
            document.getElementById('modalCreneauTitre').textContent = 'Ajouter un creneau';
            document.getElementById('creneauId').value = '';
            document.getElementById('creneauJour').value = 'Lundi';
            document.getElementById('creneauHeureDebut').value = '';
            document.getElementById('creneauHeureFin').value = '';
            document.getElementById('creneauMatiere').value = '';
            document.getElementById('creneauProfesseur').value = '';
            document.getElementById('creneauSalle').value = '';
        }

        if (!modalCreneau) modalCreneau = new bootstrap.Modal(document.getElementById('modalCreneau'));
        modalCreneau.show();
    }

    document.getElementById('btnAjouterCreneau').addEventListener('click', function() { ouvrirModal(null); });

    document.getElementById('btnValiderCreneau').addEventListener('click', function() {
        var payload = {
            id: document.getElementById('creneauId').value || null,
            niveau_id: niveauSelectionne,
            classe_id: classeSelectionnee,
            annee_scolaire: anneeSelectionnee,
            jour: document.getElementById('creneauJour').value,
            heure_debut: document.getElementById('creneauHeureDebut').value,
            heure_fin: document.getElementById('creneauHeureFin').value,
            matiere_id: document.getElementById('creneauMatiere').value,
            professeur_id: document.getElementById('creneauProfesseur').value || null,
            salle: document.getElementById('creneauSalle').value || null
        };

        if (!payload.jour || !payload.heure_debut || !payload.heure_fin || !payload.matiere_id) {
            toast('Jour, horaires et matiere sont obligatoires.', 'error');
            return;
        }

        loader(true, 'Enregistrement...');
        apiFetch(baseUrl + '/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(function(data) {
            toast(data.message, 'success');
            modalCreneau.hide();
            return chargerEtAfficherCreneaux();
        }).catch(function(e) {
            toast(e.message, 'error');
        }).finally(function() {
            loader(false);
        });
    });

    document.getElementById('btnImprimer').addEventListener('click', function() {
        if (!dernierCreneaux || !dernierCreneaux.length) {
            toast('Aucun creneau a imprimer.', 'error');
            return;
        }

        var classeNom = document.getElementById('classeSelect').selectedOptions[0].textContent;
        var html = genererHtmlImpression(dernierCreneaux, classeNom, anneeSelectionnee);

        var fenetre = window.open('', '_blank', 'width=1100,height=780');
        if (!fenetre) {
            toast('Veuillez autoriser les pop-ups pour imprimer.', 'error');
            return;
        }
        fenetre.document.open();
        fenetre.document.write(html);
        fenetre.document.close();

        fenetre.onload = function() {
            fenetre.focus();
            fenetre.print();
        };
    });

    function genererHtmlImpression(creneaux, classeNom, annee) {
        var plagesSet = {};
        var plagesListe = [];
        creneaux.forEach(function(c) {
            var key = c.heure_debut + '-' + c.heure_fin;
            if (!plagesSet[key]) {
                plagesSet[key] = true;
                plagesListe.push({ debut: c.heure_debut, fin: c.heure_fin });
            }
        });
        plagesListe.sort(function(a, b) { return a.debut.localeCompare(b.debut); });

        var theadHtml = '<th style="width:110px;">Horaire</th>';
        jours.forEach(function(j) { theadHtml += '<th>' + j + '</th>'; });

        var tbodyHtml = '';
        if (!plagesListe.length) {
            tbodyHtml = '<tr><td colspan="' + (jours.length + 1) + '" style="padding:20px;">Aucun cours programme</td></tr>';
        } else {
            plagesListe.forEach(function(plage) {
                var ligne = '<tr><td class="horaire">' + formatHeure(plage.debut) + ' - ' + formatHeure(plage.fin) + '</td>';
                jours.forEach(function(jour) {
                    var creneau = creneaux.find(function(c) {
                        return c.jour === jour && c.heure_debut === plage.debut && c.heure_fin === plage.fin;
                    });
                    if (!creneau) {
                        ligne += '<td></td>';
                    } else {
                        var cellule = '<td>';
                        cellule += '<div class="p-matiere">' + creneau.matiere_nom + '</div>';
                        if (creneau.professeur_nom) {
                            cellule += '<div class="p-prof">' + (creneau.professeur_prenom || '') + ' ' + creneau.professeur_nom + '</div>';
                        }
                        if (creneau.salle) {
                            cellule += '<div class="p-salle">' + creneau.salle + '</div>';
                        }
                        cellule += '</td>';
                        ligne += cellule;
                    }
                });
                ligne += '</tr>';
                tbodyHtml += ligne;
            });
        }

        var dateGeneration = new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
        var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' + encodeURIComponent(window.location.href);

        var headerInfos = '';
        if (ecoleInfo.ministere) {
            headerInfos += '<p class="ecole-contact" style="margin-bottom:2px;">' + ecoleInfo.ministere + '</p>';
        }
        headerInfos += '<p class="ecole-nom">' + (ecoleInfo.nom || '') + '</p>';
        if (ecoleInfo.slogan) {
            headerInfos += '<p class="ecole-slogan">&laquo; ' + ecoleInfo.slogan + ' &raquo;</p>';
        }
        headerInfos += '<p class="ecole-contact">' + (ecoleInfo.adresse || '') + (ecoleInfo.telephone ? ' &mdash; Tel : ' + ecoleInfo.telephone : '') + '</p>';

        var doc = '';
        doc += '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">';
        doc += '<title>Emploi du temps - ' + classeNom + '</title>';
        doc += '<style>';
        doc += '* { box-sizing: border-box; }';
        doc += 'body { font-family: Arial, Helvetica, sans-serif; color: #111827; margin: 0; padding: 24px; }';
        doc += '.print-header { display: flex; align-items: center; gap: 16px; border-bottom: 3px solid #1d4ed8; padding-bottom: 12px; margin-bottom: 16px; }';
        doc += '.print-header img { width: 70px; height: 70px; object-fit: contain; }';
        doc += '.print-header .ecole-infos { flex: 1; }';
        doc += '.print-header .ecole-nom { font-size: 20px; font-weight: 800; color: #1d4ed8; margin: 0; }';
        doc += '.print-header .ecole-slogan { font-size: 12px; font-style: italic; color: #64748b; margin: 0; }';
        doc += '.print-header .ecole-contact { font-size: 11px; color: #475569; margin: 0; }';
        doc += '.print-titre { text-align: center; margin: 10px 0 18px; }';
        doc += '.print-titre h4 { font-weight: 800; text-transform: uppercase; margin: 0; color: #111827; }';
        doc += '.print-titre .sous-titre { font-size: 14px; color: #334155; margin-top: 4px; }';
        doc += 'table.print-table { width: 100%; border-collapse: collapse; font-size: 12px; }';
        doc += 'table.print-table th, table.print-table td { border: 1px solid #94a3b8; padding: 6px 8px; text-align: center; vertical-align: middle; }';
        doc += 'table.print-table thead th { background: #eff6ff; color: #1d4ed8; font-weight: 700; }';
        doc += 'table.print-table td.horaire { background: #f8fafc; font-weight: 700; white-space: nowrap; }';
        doc += 'table.print-table td .p-matiere { font-weight: 700; }';
        doc += 'table.print-table td .p-prof { font-size: 10px; color: #475569; }';
        doc += 'table.print-table td .p-salle { font-size: 10px; color: #64748b; }';
        doc += '.print-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 30px; }';
        doc += '.print-footer .qr-zone { text-align: center; font-size: 10px; color: #64748b; }';
        doc += '.print-footer .signature-zone { text-align: center; min-width: 220px; }';
        doc += '.print-footer .signature-zone .ligne { margin-top: 40px; border-top: 1px solid #334155; padding-top: 4px; font-size: 12px; font-weight: 600; }';
        doc += '.print-date { font-size: 11px; color: #64748b; margin-top: 6px; }';
        doc += 'table.print-table { page-break-inside: auto; }';
        doc += 'table.print-table tr { page-break-inside: avoid; }';
        doc += 'table.print-table thead { display: table-header-group; }';
        doc += '@page { size: landscape; margin: 12mm; }';
        doc += '</style></head><body>';

        doc += '<div class="print-header">';
        doc += '<img src="' + ecoleInfo.logo + '" alt="Logo">';
        doc += '<div class="ecole-infos">' + headerInfos + '</div>';
        doc += '</div>';

        doc += '<div class="print-titre">';
        doc += '<h4>Emploi du temps</h4>';
        doc += '<div class="sous-titre">Classe : <strong>' + classeNom + '</strong> &nbsp;|&nbsp; Annee scolaire : <strong>' + annee + '</strong></div>';
        doc += '</div>';

        doc += '<table class="print-table"><thead><tr>' + theadHtml + '</tr></thead><tbody>' + tbodyHtml + '</tbody></table>';

        doc += '<div class="print-footer">';
        doc += '<div class="qr-zone"><img src="' + qrUrl + '" width="90" height="90" alt="QR Code"><div>Scanner pour consulter en ligne</div></div>';
        doc += '<div class="signature-zone"><div class="ligne">Le Directeur<br>' + (ecoleInfo.directeur || '') + '</div></div>';
        doc += '</div>';

        doc += '<div class="print-date">Document genere le ' + dateGeneration + '</div>';
        doc += '</body></html>';

        return doc;
    }
})();