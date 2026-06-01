<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    private mysqli $db;

    public function __construct()
    {
        $this->db =
            Database::getConnection();
    }

    public function generate(
        int $offerId
    ): array
    {
        $offer =
            $this->getOffer(
                $offerId
            );

        if(!$offer)
        {
            throw new Exception(
                'Offer not found'
            );
        }

        $html =
            $this->renderHtml(
                $offer
            );

        $offerNumber =
            $offer['offer']
            ['numaroferta'];

        $baseName =
            'OF-' .
            $offerNumber;

        $htmlFile =
            __DIR__ .
            '/../storage/html/' .
            $baseName .
            '.html';

        file_put_contents(
            $htmlFile,
            $html
        );

        $options =
            new Options();

        $options->set(
            'isRemoteEnabled',
            true
        );

        $dompdf =
            new Dompdf(
                $options
            );

        $dompdf->loadHtml(
            $html
        );

        $dompdf->setPaper(
            'A4',
            'portrait'
        );

        $dompdf->render();

        $pdfPath =
            __DIR__ .
            '/../storage/pdfs/' .
            $baseName .
            '.pdf';

        file_put_contents(
            $pdfPath,
            $dompdf->output()
        );

        $stmt =
            $this->db->prepare(
            "
            UPDATE oferte
            SET
                pdf_file=?,
                html_file=?,
                pdf_generated_at=NOW()
            WHERE id=?
            "
        );

        $pdfDb =
            $baseName .
            '.pdf';

        $htmlDb =
            $baseName .
            '.html';

        $stmt->bind_param(
            'ssi',
            $pdfDb,
            $htmlDb,
            $offerId
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
}

        return [
            'pdf' => $pdfDb,
            'html' => $htmlDb
        ];
    }

    private function getOffer(
        int $offerId
    ): ?array
    {
        $stmt =
            $this->db->prepare(
            "
            SELECT *
            FROM oferte
            WHERE deleted=0 and id=?
            "
        );

        $stmt->bind_param(
            'i',
            $offerId
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
}

        $offer =
            $stmt
            ->get_result()
            ->fetch_assoc();

        if(!$offer)
        {
            return null;
        }

        $stmt =
            $this->db->prepare(
            "
            SELECT *
            FROM elementeoferte
            WHERE oferta=?
            ORDER BY id
            "
        );

        $stmt->bind_param(
            'i',
            $offerId
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
}

        $items =
            $stmt
            ->get_result()
            ->fetch_all(
                MYSQLI_ASSOC
            );

        return [
            'offer' => $offer,
            'items' => $items
        ];
    }

    private function renderHtml(
        array $data
    ): string
    {
        ob_start();

        $offer =
            $data['offer'];

        $items =
            $data['items'];

        include
            __DIR__ .
            '/../templates/offer-template.php';

        return ob_get_clean();
    }
}