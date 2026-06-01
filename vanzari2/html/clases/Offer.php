<?php

class Offer
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(
        string $search = '',
        string $status = '',
        string $dateFrom = '',
        string $dateTo = ''
    ): array
    {
        $sql = "
            SELECT
                o.*,
                COUNT(e.id) AS item_count,
                COALESCE(
                    SUM(
                        CAST(
                            NULLIF(e.total,'')
                            AS DECIMAL(15,2)
                        )
                    ),
                    0
                ) AS calculated_total
            FROM oferte o
            LEFT JOIN elementeoferte e
                ON e.oferta = o.id
            WHERE o.deleted=0 and 1=1
        ";

        $types = '';
        $params = [];

        if ($search !== '')
        {
            $sql .= "
                AND (
                    o.firma LIKE ?
                    OR o.createdby LIKE ?
                    OR o.responsabil LIKE ?
                    OR o.numaroferta LIKE ?
                )
            ";

            $searchLike = "%{$search}%";

            $types .= 'ssss';

            $params[] = $searchLike;
            $params[] = $searchLike;
            $params[] = $searchLike;
            $params[] = $searchLike;
        }

        if ($status !== '')
        {
            $sql .= " AND o.stareoferta = ? ";

            $types .= 's';
            $params[] = $status;
        }

        if ($dateFrom !== '')
        {
            $sql .= " AND o.data >= ? ";

            $types .= 's';
            $params[] = $dateFrom;
        }

        if ($dateTo !== '')
        {
            $sql .= " AND o.data <= ? ";

            $types .= 's';
            $params[] = $dateTo;
        }

        $sql .= "
            GROUP BY o.id
            ORDER BY o.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        if (!empty($params))
        {
            $stmt->bind_param($types, ...$params);
        }

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT *
            FROM oferte
            WHERE deleted=0 and id=?"
        );

        $stmt->bind_param('i',$id);

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        return $stmt
            ->get_result()
            ->fetch_assoc();
    }

    public function getNextOfferNumber(): int
    {
        $result = $this->db->query(
            "SELECT MAX(numaroferta) max_no
             FROM oferte where deleted=0"
        );

        $row = $result->fetch_assoc();

        return ((int)$row['max_no']) + 1;
    }

    public function create(array $data): int
    {
        $nextNo = $this->getNextOfferNumber();

        $stmt = $this->db->prepare(
            "INSERT INTO oferte
            (
                numaroferta,
                firma,
                data,
                createdby,
                observatii,
                responsabil,
                valuta,
                departament,
                stareoferta,
                contact_client,
                email_client
            )
            VALUES
            (
                ?,?,?,?,?,?,?,?,?,?,?
            )"
        );

        $stmt->bind_param(
            'issssssssss',
            $nextNo,
            $data['firma'],
            $data['data'],
            $data['createdby'],
            $data['observatii'],
            $data['responsabil'],
            $data['valuta'],
            $data['departament'],
            $data['stareoferta'],
            $data['contact_client'],
            $data['email_client']
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
        $stmt = $this->db->prepare(
            "UPDATE oferte
            SET
                firma=?,
                data=?,
                observatii=?,
                responsabil=?,
                valuta=?,
                departament=?,
                stareoferta=?,
                contact_client=?,
                email_client=?,
                updatedBy=?
            WHERE id=?"
        );

        $stmt->bind_param(
            'ssssssssssi',
            $data['firma'],
            $data['data'],
            $data['observatii'],
            $data['responsabil'],
            $data['valuta'],
            $data['departament'],
            $data['stareoferta'],
            $data['contact_client'],
            $data['email_client'],
            $data['updatedBy'],
            $id
        );

        return if(!$stmt->execute()){
    throw new Exception($stmt->error);
};
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM elementeoferte
             WHERE oferta=?"
        );

        $stmt->bind_param('i', $id);

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        $stmt = $this->db->prepare(
            "UPDATE oferte
                SET deleted=1
                WHERE id=?"
        );

        $stmt->bind_param('i', $id);

        return if(!$stmt->execute()){
    throw new Exception($stmt->error);
};
    }

    public function duplicate(
        int $id,
        string $user
    ): int
    {
        $original = $this->getById($id);

        if (!$original)
        {
            throw new Exception(
                'Offer not found'
            );
        }

        $offer = $original['offer'];

        $newOfferId = $this->create([
            'firma' => $offer['firma'],
            'data' => date('Y-m-d'),
            'createdby' => $user,
            'observatii' => $offer['observatii'],
            'responsabil' => $offer['responsabil'],
            'valuta' => $offer['valuta'],
            'departament' => $offer['departament'],
            'stareoferta' => 'Draft',
            'contact_client' => $offer['contact_client'],
            'email_client' => $offer['email_client']
        ]);

        foreach ($original['items'] as $item)
        {
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
                    catalog_no,
                    material_no,
                    packing_q,
                    cod_client,
                    obs1,
                    obs2
                )
                VALUES
                (
                    ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
                )"
            );

            $stmt->bind_param(
                'issssssssssssss',
                $newOfferId,
                $item['cod'],
                $item['descriere'],
                $item['pret'],
                $item['buc'],
                $item['discount'],
                $item['livrare'],
                $item['valoare'],
                $item['total'],
                $item['catalog_no'],
                $item['material_no'],
                $item['packing_q'],
                $item['cod_client'],
                $item['obs1'],
                $item['obs2']
            );

            if(!$stmt->execute()){
    throw new Exception($stmt->error);
};
        }

        return $newOfferId;
    }
}