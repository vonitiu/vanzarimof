<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private mysqli $db;

    public function __construct()
    {
        $this->db =
            Database::getConnection();
    }

    public function sendOffer(
        int $offerId,
        string $recipient,
        string $subject,
        string $body,
        string $username
    ): bool
    {
        $offer =
            $this->getOffer(
                $offerId
            );

        if (!$offer)
        {
            throw new Exception(
                'Offer not found'
            );
        }

        $pdfFile =
            __DIR__ .
            '/../storage/pdfs/' .
            $offer['pdf_file'];

        if (!file_exists($pdfFile))
        {
            throw new Exception(
                'PDF not generated'
            );
        }

        $mail =
            new PHPMailer(true);

        try
        {
            $mail->isSMTP();

            $mail->Host =
                SMTP_HOST;

            $mail->SMTPAuth = true;

            $mail->Username =
                SMTP_USERNAME;

            $mail->Password =
                SMTP_PASSWORD;

            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port =
                SMTP_PORT;

            $mail->setFrom(
                SMTP_FROM,
                SMTP_FROM_NAME
            );

            $mail->addAddress(
                $recipient
            );

            $mail->isHTML(true);

            $mail->Subject =
                $subject;

            $mail->Body =
                $body;

            $mail->addAttachment(
                $pdfFile
            );

            $mail->send();

            $this->logEmail(
                $offerId,
                $recipient,
                $subject,
                'SUCCESS',
                '',
                $username
            );

            $this->markOfferSent(
                $offerId,
                $recipient
            );

            return true;
        }
        catch(Exception $e)
        {
            $this->logEmail(
                $offerId,
                $recipient,
                $subject,
                'FAILED',
                $e->getMessage(),
                $username
            );

            throw $e;
        }
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

        return
            $stmt
            ->get_result()
            ->fetch_assoc();
    }

    private function markOfferSent(
        int $offerId,
        string $recipient
    ): void
    {
        $trackId =
            bin2hex(
                random_bytes(16)
            );

        $stmt =
            $this->db->prepare(
            "
            UPDATE oferte
            SET
                email_sent=1,
                email_sent_at=NOW(),
                email_recipient=?,
                email_track_id=?
            WHERE id=?
            "
        );

        $stmt->bind_param(
            'ssi',
            $recipient,
            $trackId,
            $offerId
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
}
    }

    private function logEmail(
        int $offerId,
        string $recipient,
        string $subject,
        string $status,
        string $error,
        string $user
    ): void
    {
        $stmt =
            $this->db->prepare(
            "
            INSERT INTO email_history
            (
                offer_id,
                recipient,
                subject,
                status,
                error_message,
                sent_by,
                sent_at
            )
            VALUES
            (
                ?,?,?,?,?,?,NOW()
            )
            "
        );

        $stmt->bind_param(
            'isssss',
            $offerId,
            $recipient,
            $subject,
            $status,
            $error,
            $user
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
}
    }
}