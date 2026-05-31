<?php

class Item
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByOffer(
        int $offerId
    ): array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM elementeoferte
             WHERE oferta=?
             ORDER BY id ASC"
        );

        $stmt->bind_param(
            'i',
            $offerId
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        return $stmt
            ->get_result()
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

    public function getById(
        int $id
    ): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM elementeoferte
             WHERE id=?"
        );

        $stmt->bind_param(
            'i',
            $id
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        $item = $stmt
            ->get_result()
            ->fetch_assoc();

        return $item ?: null;
    }

    private function calculate(
        array &$data
    ): void
    {
        $qty = (float)(
            $data['buc'] ?? 0
        );

        $price = (float)(
            $data['pret'] ?? 0
        );

        $discount = (float)(
            $data['discount'] ?? 0
        );

        $value =
            $qty * $price;

        $total =
            $value - $discount;

        $data['valoare'] =
            number_format(
                $value,
                2,
                '.',
                ''
            );

        $data['total'] =
            number_format(
                $total,
                2,
                '.',
                ''
            );
    }

    public function create(
        array $data
    ): int
    {
        $this->calculate(
            $data
        );

        $stmt = $this->db->prepare(
            "INSERT INTO elementeoferte
            (
                oferta,
                cod,
                descriere,
                pret,
                buc,
                discount,
                livrare,
                valoare,
                total,
                selected,
                catalog_no,
                material_no,
                packing_q,
                cod_client,
                obs1,
                obs2
            )
            VALUES
            (
                ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
            )"
        );

        $selected =
            $data['selected']
            ?? 0;

        $stmt->bind_param(
            'isssssssisssssss',

            $data['oferta'],
            $data['cod'],
            $data['descriere'],
            $data['pret'],
            $data['buc'],
            $data['discount'],
            $data['livrare'],
            $data['valoare'],
            $data['total'],
            $selected,
            $data['catalog_no'],
            $data['material_no'],
            $data['packing_q'],
            $data['cod_client'],
            $data['obs1'],
            $data['obs2']
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        return $this->db->insert_id;
    }

    public function update(
        int $id,
        array $data
    ): bool
    {
        $this->calculate(
            $data
        );

        $selected =
            $data['selected']
            ?? 0;

        $stmt = $this->db->prepare(
            "UPDATE elementeoferte
            SET
                cod=?,
                descriere=?,
                pret=?,
                buc=?,
                discount=?,
                livrare=?,
                valoare=?,
                total=?,
                selected=?,
                catalog_no=?,
                material_no=?,
                packing_q=?,
                cod_client=?,
                obs1=?,
                obs2=?
            WHERE id=?"
        );

        $stmt->bind_param(
            'ssssssssissssssi',

            $data['cod'],
            $data['descriere'],
            $data['pret'],
            $data['buc'],
            $data['discount'],
            $data['livrare'],
            $data['valoare'],
            $data['total'],
            $selected,
            $data['catalog_no'],
            $data['material_no'],
            $data['packing_q'],
            $data['cod_client'],
            $data['obs1'],
            $data['obs2'],
            $id
        );

        return if(!$stmt->execute()){
    throw new Exception($stmt->error);
};
    }

    public function delete(
        int $id
    ): bool
    {
        $stmt = $this->db->prepare(
            "DELETE
             FROM elementeoferte
             WHERE id=?"
        );

        $stmt->bind_param(
            'i',
            $id
        );

        return if(!$stmt->execute()){
    throw new Exception($stmt->error);
};
    }

    public function updateOfferTotal(
        int $offerId
    ): void
    {
        $stmt = $this->db->prepare(
            "
            SELECT
                COALESCE(
                    SUM(
                        CAST(
                            total AS DECIMAL(15,2)
                        )
                    ),
                    0
                ) total
            FROM elementeoferte
            WHERE oferta=?
            "
        );

        $stmt->bind_param(
            'i',
            $offerId
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        $row =
            $stmt
            ->get_result()
            ->fetch_assoc();

        $offerTotal =
            number_format(
                $row['total'],
                2,
                '.',
                ''
            );

        $stmt = $this->db->prepare(
            "
            UPDATE oferte
            SET suma=?
            WHERE id=?
            "
        );

        $stmt->bind_param(
            'si',
            $offerTotal,
            $offerId
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};
    }
}