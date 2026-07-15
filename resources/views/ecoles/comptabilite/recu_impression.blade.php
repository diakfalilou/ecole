<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Reçu {{ $numeroRecu }}</title>
<style>
    :root {
        --ink: #1a2233;
        --ink-soft: #4a5568;
        --line: #d9dee6;
        --accent: #1d4e89;
        --accent-soft: #eaf1fb;
        --paid: #1b7a43;
        --paid-bg: #e9f7ee;
        --due: #a4341f;
        --due-bg: #fbeceA;
        --exo: #8a5a00;
        --exo-bg: #fdf3dc;
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Georgia', 'Times New Roman', serif;
        color: var(--ink);
        background: #eef1f5;
        margin: 0;
        padding: 24px;
    }

    /* ===== Page A4 contenant les 2 exemplaires ===== */
    .page-a4 {
        max-width: 780px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
    }

    /* Chaque exemplaire occupe exactement la moitié de la page */
    .exemplaire {
        flex: 1 1 50%;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* ===== Étiquette d'exemplaire ===== */
    .etiquette-copie {
        flex: 0 0 auto;
        text-align: center;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        font-size: 9px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #fff;
        background: var(--ink-soft);
        padding: 2px 0;
        margin-bottom: 4px;
    }

    /* ===== Ligne de coupe entre les deux exemplaires ===== */
    .ligne-coupe {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 4px 0;
        color: var(--ink-soft);
        font-size: 10px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }
    .ligne-coupe .trait {
        flex: 1;
        border-top: 1px dashed var(--line);
    }

    .feuille {
        flex: 1 1 auto;
        min-height: 0;
        background: #fff;
        border: 1px solid var(--line);
        padding: 12px 22px;
        position: relative;
        overflow: hidden;
    }

    /* ===== En-tête école ===== */
    .entete {
        display: flex;
        align-items: center;
        gap: 14px;
        border-bottom: 2px solid var(--accent);
        padding-bottom: 8px;
        margin-bottom: 8px;
    }
    .entete img.logo {
        width: 42px;
        height: 42px;
        object-fit: contain;
        flex-shrink: 0;
    }
    .entete .infos-ecole h1 {
        font-size: 15px;
        letter-spacing: 0.03em;
        margin: 0 0 2px;
        color: var(--accent);
        text-transform: uppercase;
    }
    .entete .infos-ecole p {
        margin: 0;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        font-size: 10px;
        color: var(--ink-soft);
        line-height: 1.4;
    }

    /* ===== Titre reçu ===== */
    .titre-recu {
        text-align: center;
        margin: 8px 0 8px;
    }
    .titre-recu .badge-mode {
        display: inline-block;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        font-size: 10px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--accent);
        background: var(--accent-soft);
        border: 1px solid var(--accent);
        border-radius: 3px;
        padding: 2px 12px;
        margin-bottom: 4px;
    }
    .titre-recu h2 {
        font-size: 17px;
        margin: 4px 0 0;
        letter-spacing: 0.02em;
    }

    /* ===== Meta (numéro / date / année) ===== */
    .meta {
        display: flex;
        justify-content: space-between;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        font-size: 10.5px;
        color: var(--ink-soft);
        border-top: 1px solid var(--line);
        border-bottom: 1px solid var(--line);
        padding: 5px 2px;
        margin-bottom: 8px;
    }
    .meta strong { color: var(--ink); }

    /* ===== Élève ===== */
    .bloc-eleve {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px 24px;
        font-size: 11.5px;
        margin-bottom: 8px;
    }
    .bloc-eleve .label {
        color: var(--ink-soft);
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        display: block;
    }
    .bloc-eleve .valeur {
        font-weight: 600;
    }

    /* ===== Exonération (affichée uniquement si applicable) ===== */
    .bloc-exoneration {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        background: var(--exo-bg);
        border: 1px solid var(--exo);
        color: var(--exo);
        border-radius: 4px;
        padding: 5px 12px;
        font-size: 10.5px;
        margin-bottom: 8px;
    }
    .bloc-exoneration strong { display: block; font-size: 11px; margin-bottom: 1px; }

    /* ===== Tableau détail du reçu ===== */
    table.detail {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        font-size: 10.5px;
        margin-bottom: 8px;
    }
    table.detail th {
        background: var(--accent-soft);
        color: var(--accent);
        text-transform: uppercase;
        font-size: 9px;
        letter-spacing: 0.04em;
        text-align: left;
        padding: 3px 8px;
        border-bottom: 2px solid var(--accent);
    }
    table.detail td {
        padding: 3px 8px;
        border-bottom: 1px solid var(--line);
    }
    table.detail tfoot td {
        border-top: 2px solid var(--accent);
        border-bottom: none;
        font-weight: 700;
        font-size: 11.5px;
        padding-top: 6px;
    }

    /* ===== Suivi paiement (dû / payé / reste) ===== */
    .suivi {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        border: 1px solid var(--line);
        border-radius: 4px;
        padding: 7px 12px;
        margin-bottom: 8px;
    }
    .suivi h3 {
        margin: 0 0 6px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--ink-soft);
    }
    .suivi-chiffres {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 5px;
    }
    .chiffre {
        flex: 1;
        min-width: 120px;
        padding: 5px 10px;
        border-radius: 4px;
        background: #f5f7fa;
    }
    .chiffre .label { display: block; font-size: 9px; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.04em; }
    .chiffre .val { display: block; font-size: 13px; font-weight: 700; margin-top: 1px; }
    .chiffre.paye { background: var(--paid-bg); }
    .chiffre.paye .val { color: var(--paid); }
    .chiffre.reste { background: var(--due-bg); }
    .chiffre.reste .val { color: var(--due); }

    .mois-liste { font-size: 10.5px; }
    .mois-liste .grp { margin-bottom: 3px; }
    .mois-liste .grp .label { color: var(--ink-soft); text-transform: uppercase; font-size: 9px; letter-spacing: 0.04em; margin-right: 6px; }
    .pastille {
        display: inline-block;
        font-size: 9.5px;
        padding: 1px 7px;
        border-radius: 10px;
        margin: 1px 3px 1px 0;
    }
    .pastille.ok { background: var(--paid-bg); color: var(--paid); }
    .pastille.non { background: var(--due-bg); color: var(--due); }

    /* ===== Pied de page / signature ===== */
    .pied {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 8px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        font-size: 10px;
        color: var(--ink-soft);
    }
    .signature {
        text-align: center;
    }
    .signature .ligne {
        width: 130px;
        border-top: 1px solid var(--ink-soft);
        margin-top: 14px;
        padding-top: 3px;
    }

    /* ===== Bouton d'impression (masqué à l'impression) ===== */
    .barre-actions {
        max-width: 780px;
        margin: 0 auto 16px;
        text-align: right;
    }
    .btn-imprimer {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        background: var(--accent);
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
    }
    .btn-imprimer:hover { opacity: 0.9; }

    @media print {
        @page { size: A4 portrait; margin: 8mm; }
        html, body {
            background: #fff;
            padding: 0;
            margin: 0;
            height: 281mm; /* hauteur imprimable A4 (297mm - 2x8mm de marge) */
        }
        .barre-actions { display: none; }
        .page-a4 {
            max-width: 100%;
            height: 281mm;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        .exemplaire {
            page-break-inside: avoid;
        }
        .etiquette-copie,
        .chiffre,
        .pastille {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .feuille { border: none; }
    }
</style>
</head>
<body>

    <div class="barre-actions">
        <button class="btn-imprimer" onclick="window.print()">🖨️ Imprimer le reçu (2 exemplaires)</button>
    </div>

    <div class="page-a4">

        @foreach(['Exemplaire — Scolarité', 'Exemplaire — Élève'] as $i => $copie)

            @if($i > 0)
                <div class="ligne-coupe">
                    <span class="trait"></span>
                    <span>✂ Découper ici</span>
                    <span class="trait"></span>
                </div>
            @endif

            <div class="exemplaire">

            <div class="etiquette-copie">{{ $copie }}</div>

            <div class="feuille">

                {{-- ===== En-tête école ===== --}}
                <div class="entete">
                    @if(!empty($ecole->logo ?? null))
                        <img class="logo" src="{{ asset($ecole->logo) }}" alt="Logo de l'école">
                    @endif
                    <div class="infos-ecole">
                        <h1>{{ $ecole->v_nomecole ?? $ecole->v_nom ?? 'Établissement scolaire' }}</h1>
                        <p>
                            @if(!empty($ecole->v_adresse)) {{ $ecole->v_adresse }} @endif
                            @if(!empty($ecole->v_telephone)) &nbsp;·&nbsp; Tél : {{ $ecole->v_telephone }} @endif
                            @if(!empty($ecole->v_email)) &nbsp;·&nbsp; {{ $ecole->v_email }} @endif
                        </p>
                    </div>
                </div>

                {{-- ===== Titre ===== --}}
                <div class="titre-recu">
                    <span class="badge-mode">{{ $modeLabel }}</span>
                    <h2>Reçu de paiement</h2>
                </div>

                {{-- ===== Meta ===== --}}
                <div class="meta">
                    <span>N° Reçu : <strong>{{ $numeroRecu }}</strong></span>
                    <span>Date : <strong>{{ $date }}</strong></span>
                    <span>Année scolaire : <strong>{{ $anneeSco }}</strong></span>
                </div>

                {{-- ===== Élève ===== --}}
                <div class="bloc-eleve">
                    <div>
                        <span class="label">Élève</span>
                        <span class="valeur">{{ $eleve->v_nom ?? '' }} {{ $eleve->v_prenom ?? '' }}</span>
                    </div>
                    <div>
                        <span class="label">Matricule</span>
                        <span class="valeur">{{ $eleve->v_matricule ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="label">Niveau</span>
                        <span class="valeur">{{ $niveau->v_niveaux ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="label">Classe</span>
                        <span class="valeur">{{ $classe->v_nom_classe ?? 'N/A' }}</span>
                    </div>
                </div>

                {{-- ===== Exonération : uniquement si elle existe réellement ===== --}}
                @if($exoneration)
                    <div class="bloc-exoneration">
                        <strong>Exonération appliquée</strong>
                        @if($exoneration->v_type_exoneration === 'totale')
                            Exonéré à 100% sur ce mode de paiement, autorisé par {{ $exoneration->v_autorise_par }}.
                        @else
                            Exonéré à {{ (float) $exoneration->f_pourcentage }}%
                            (soit {{ number_format((float) $exoneration->f_montant_exonere, 0, ',', ' ') }} GNF déduits),
                            autorisé par {{ $exoneration->v_autorise_par }}.
                        @endif
                    </div>
                @endif

                {{-- ===== Détail des lignes de CE reçu ===== --}}
                <table class="detail">
                    <thead>
                        <tr>
                            <th>Mois</th>
                            <th>Mode</th>
                            <th style="text-align:right;">Montant (GNF)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $ligne)
                            <tr>
                                <td>{{ $ligne['mois'] }}</td>
                                <td>{{ $modeLabels[$ligne['mode']] ?? $ligne['mode'] }}</td>
                                <td style="text-align:right;">{{ number_format((float) $ligne['montant'], 0, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Total payé sur ce reçu</td>
                            <td style="text-align:right;">{{ number_format((float) $montantTotal, 0, ',', ' ') }} GNF</td>
                        </tr>
                    </tfoot>
                </table>

                {{-- ===== Suivi global du mode de paiement (dû / payé / reste) ===== --}}
                @if($suivi)
                    <div class="suivi">
                        <h3>Suivi — {{ $modeLabel }} @if($anneeSco) ({{ $anneeSco }}) @endif</h3>
                        <div class="suivi-chiffres">
                            <div class="chiffre">
                                <span class="label">Montant total dû</span>
                                <span class="val">{{ number_format($suivi['montant_du'], 0, ',', ' ') }} GNF</span>
                            </div>
                            <div class="chiffre paye">
                                <span class="label">Total déjà payé</span>
                                <span class="val">{{ number_format($suivi['montant_paye'], 0, ',', ' ') }} GNF</span>
                            </div>
                            <div class="chiffre reste">
                                <span class="label">Reste à payer</span>
                                <span class="val">{{ number_format($suivi['reste'], 0, ',', ' ') }} GNF</span>
                            </div>
                        </div>

                        @if($modeReel === 'mensuelle')
                            <div class="mois-liste">
                                <div class="grp">
                                    <span class="label">Mois payés :</span>
                                    @forelse($suivi['mois_payes'] as $m)
                                        <span class="pastille ok">{{ $m }} ✓</span>
                                    @empty
                                        <span class="pastille non">Aucun</span>
                                    @endforelse
                                </div>
                                <div class="grp">
                                    <span class="label">Mois restants :</span>
                                    @forelse($suivi['mois_non_payes'] as $m)
                                        <span class="pastille non">{{ $m }}</span>
                                    @empty
                                        <span class="pastille ok">Aucun — année soldée</span>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ===== Pied / signature ===== --}}
                <div class="pied">
                    <span>Ce reçu fait foi de paiement. Merci de le conserver.</span>
                    <div class="signature">
                        <div class="ligne">Signature / Cachet</div>
                    </div>
                </div>

            </div>

            </div>

        @endforeach

    </div>

</body>
</html>
