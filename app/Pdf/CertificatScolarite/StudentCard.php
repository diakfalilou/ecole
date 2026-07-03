<?php

namespace App\Pdf\CertificatScolarite;

class StudentCard
{
    public static function render(
        $pdf,
        $eleve,
        $niveau,
        $classe,
        $annee
    ) {

        //==============================
        // TITRE
        //==============================

        $pdf->SetFillColor(235,235,235);

        $pdf->SetFont('helvetica','B',11);

        $pdf->Cell(
            0,
            8,
            "INFORMATIONS DE L'ELEVE",
            0,
            1,
            'C',
            true
        );

        $pdf->Ln(2);

        //==============================
        // DIMENSIONS
        //==============================

        $x = 15;
        $y = $pdf->GetY();

        $largeurInfo = 135;
        $largeurPhoto = 40;
        $hauteur = 58;

        //==============================
        // CADRE
        //==============================

        $pdf->Rect(
            $x,
            $y,
            $largeurInfo + $largeurPhoto,
            $hauteur
        );

        // séparation verticale

        $pdf->Line(
            $x + $largeurInfo,
            $y,
            $x + $largeurInfo,
            $y + $hauteur
        );

        //==============================
        // INFOS
        //==============================

        $pdf->SetFont('courier','',10);

        $ligne = 7;

        $pdf->SetXY($x+3,$y+4);

        $pdf->Cell(60,$ligne,"Matricule :",0,0);
        $pdf->Cell(70,$ligne,$eleve->v_matricule ?? '',0,1);

        $pdf->SetX($x+3);
        $pdf->Cell(60,$ligne,"Nom :",0,0);
        $pdf->Cell(70,$ligne,strtoupper($eleve->v_nom ?? ''),0,1);

        $pdf->SetX($x+3);
        $pdf->Cell(60,$ligne,"Prenom :",0,0);
        $pdf->Cell(70,$ligne,strtoupper($eleve->v_prenom ?? ''),0,1);

        $pdf->SetX($x+3);
        $pdf->Cell(60,$ligne,"Sexe :",0,0);
        $pdf->Cell(70,$ligne,$eleve->v_genre ?? '',0,1);

        $date = "";

        if(!empty($eleve->d_date_naissance)){
            $date = date("d/m/Y",strtotime($eleve->d_date_naissance));
        }

        $pdf->SetX($x+3);
        $pdf->Cell(60,$ligne,"Date de naissance :",0,0);
        $pdf->Cell(70,$ligne,$date,0,1);

        $pdf->SetX($x+3);
        $pdf->Cell(60,$ligne,"Niveau :",0,0);
        $pdf->Cell(70,$ligne,$niveau,0,1);

        $pdf->SetX($x+3);
        $pdf->Cell(60,$ligne,"Classe :",0,0);
        $pdf->Cell(70,$ligne,$classe,0,1);

        $pdf->SetX($x+3);
        $pdf->Cell(60,$ligne,"Année scolaire :",0,0);
        $pdf->Cell(70,$ligne,$annee,0,1);

        //==============================
        // PHOTO
        //==============================

        $photoX = $x + $largeurInfo + 3;

        $photoY = $y + 5;

        if(
            !empty($eleve->v_photo)
            &&
            file_exists(public_path($eleve->v_photo))
        ){

            $pdf->Image(
                public_path($eleve->v_photo),
                $photoX,
                $photoY,
                32,
                38
            );

        }else{

            $pdf->Rect(
                $photoX,
                $photoY,
                32,
                38
            );

            $pdf->SetFont('helvetica','',8);

            $pdf->SetXY(
                $photoX,
                $photoY+16
            );

            $pdf->Cell(
                32,
                5,
                "PHOTO",
                0,
                0,
                'C'
            );

        }

        //==============================
        // LÉGENDE PHOTO
        //==============================

        $pdf->SetFont('helvetica','I',8);

        $pdf->SetXY(
            $photoX,
            $photoY+40
        );

        $pdf->Cell(
            32,
            5,
            "Photo d'identité",
            0,
            0,
            'C'
        );

        // $pdf->Ln(65);
    }
}
