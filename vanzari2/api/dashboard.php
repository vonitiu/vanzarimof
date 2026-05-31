<?php

require '../config/config.php';
require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$user = Auth::validate();

if(!$user)
{
    Response::json([
        'success'=>false
    ],401);
}

$db = Database::getConnection();

$summary =
$db->query("
SELECT

COUNT(*) total_offers,

SUM(
CASE
WHEN stareoferta='Draft'
THEN 1
ELSE 0
END
) drafts,

SUM(
CASE
WHEN stareoferta='Submitted'
THEN 1
ELSE 0
END
) submitted,

SUM(
CASE
WHEN stareoferta='Sent'
THEN 1
ELSE 0
END
) sent,

SUM(offer_total) total_value

FROM vw_offer_summary
")->fetch_assoc();

$topCustomers =
$db->query("
SELECT

firma,

COUNT(*) offers,

SUM(offer_total) total

FROM vw_offer_summary

WHERE firma IS NOT NULL
AND firma<>''

GROUP BY firma

ORDER BY total DESC

LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

$topAgents =
$db->query("
SELECT

responsabil,

COUNT(*) offers,

SUM(offer_total) total

FROM vw_offer_summary

WHERE responsabil IS NOT NULL
AND responsabil<>''

GROUP BY responsabil

ORDER BY total DESC

LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

$departments =
$db->query("
SELECT

departament,

COUNT(*) offers,

SUM(offer_total) total

FROM vw_offer_summary

GROUP BY departament

ORDER BY total DESC
")->fetch_all(MYSQLI_ASSOC);

$awaitingEmail =
$db->query("
SELECT

id,
numaroferta,
firma,
offer_total

FROM vw_offer_summary

WHERE email_sent=0

ORDER BY id DESC

LIMIT 20
")->fetch_all(MYSQLI_ASSOC);

$sentThisWeek =
$db->query("
SELECT COUNT(*) cnt

FROM oferte

WHERE deleted=0 and email_sent=1
AND email_sent_at >= DATE_SUB(
NOW(),
INTERVAL 7 DAY
)
")->fetch_assoc();

$recentActivity =
$db->query("
SELECT

action,
details,
user_name,
created_at

FROM audit_logs

ORDER BY created_at DESC

LIMIT 25
")->fetch_all(MYSQLI_ASSOC);

Response::json([
'success'=>true,

'summary'=>$summary,

'topCustomers'=>$topCustomers,

'topAgents'=>$topAgents,

'departments'=>$departments,

'awaitingEmail'=>$awaitingEmail,

'sentThisWeek'=>$sentThisWeek,

'recentActivity'=>$recentActivity
]);