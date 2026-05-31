create a prograsive web app that is using only html, css, js and jquery, no frameworks witha. php backend again with no frameworks. The app will be an app where sales agents can create offers for clients and those offers will be converted to pdf and sent to an email adress to be processed by another team. The app must have a login page, a page to see al offers, a page to see the datails of an existing offer offer general details(cleint, date of creation, who created it, numeber of items, total value...) + items, a page to edit or crete an offer general details with number, date, client, and a few more fields, a page that opens for each item in the item list so that items that can be edited individualy or added. The offer is represented in the db by 2 tables one for the offer info and one for the items. This is the schema:

CREATE TABLE elementeoferte (
  id int NOT NULL,
  oferta varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  cod varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  descriere varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  pret varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  buc varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  discount varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  livrare varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  valoare varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  total varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  selected int DEFAULT NULL,
  catalog_no varchar(45) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  material_no varchar(45) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  packing_q varchar(45) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  cod_client varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  obs1 varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  obs2 varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `oferte` (
  `id` int NOT NULL,
  `numaroferta` int NOT NULL,
  `firma` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `data` date DEFAULT NULL,
  `createdby` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `suma` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `discountfirma` float DEFAULT NULL,
  `observatii` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `responsabil` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `valuta` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `client` int DEFAULT NULL,
  `idcomanda` int DEFAULT NULL,
  `customobservatii` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `customobservatii2` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `departament` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `stareoferta` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `selected` int DEFAULT NULL,
  `updatedBy` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `data_notificare` varchar(45) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `contact_client` varchar(45) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `email_client` varchar(45) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `notified_by` varchar(45) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `curs_valutar` varchar(45) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `print_no_discount` int NOT NULL DEFAULT '0',
  `email_sent` tinyint(1) DEFAULT '0',
  `email_sent_at` datetime DEFAULT NULL,
  `email_track_id` varchar(36) COLLATE latin1_general_ci DEFAULT NULL,
  `email_recipient` varchar(255) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;






_________________________________


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password_hash VARCHAR(255),
    full_name VARCHAR(255),
    email VARCHAR(255),
    role ENUM(
        'admin',
        'sales',
        'manager'
    ),
    active TINYINT DEFAULT 1,
    created_at DATETIME
);

CREATE TABLE auth_tokens (
    token VARCHAR(64) PRIMARY KEY,
    user_id INT,
    expires_at DATETIME,
    created_at DATETIME
);



CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    offer_id INT,
    user_id INT,
    action VARCHAR(100),
    details TEXT,
    created_at DATETIME
);



CREATE TABLE email_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    offer_id INT,
    recipient VARCHAR(255),
    subject VARCHAR(255),
    sent_at DATETIME,
    status VARCHAR(50)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    email VARCHAR(255),
    role ENUM('admin','sales','manager') DEFAULT 'sales',
    active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE auth_tokens (
    token VARCHAR(64) PRIMARY KEY,
    user_id INT NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX(user_id)
);


composer require dompdf/dompdf

ALTER TABLE oferte
ADD COLUMN pdf_file VARCHAR(255) NULL,
ADD COLUMN html_file VARCHAR(255) NULL,
ADD COLUMN pdf_generated_at DATETIME NULL;

ALTER TABLE oferte
ADD COLUMN email_subject VARCHAR(255),
ADD COLUMN email_body TEXT;

composer require phpmailer/phpmailer

CREATE TABLE email_history
(
    id INT AUTO_INCREMENT PRIMARY KEY,

    offer_id INT NOT NULL,

    recipient VARCHAR(255),

    subject VARCHAR(255),

    status VARCHAR(50),

    error_message TEXT,

    sent_by VARCHAR(128),

    sent_at DATETIME
);

ALTER TABLE oferte
ADD COLUMN email_subject VARCHAR(255),
ADD COLUMN email_body TEXT,
ADD COLUMN `email_sent` tinyint(1) DEFAULT '0',
ADD COLUMN  `email_sent_at` datetime DEFAULT NULL,
ADD COLUMN  `email_track_id` varchar(36) ,
ADD COLUMN  `email_recipient` varchar(255) ;

CREATE TABLE email_queue
(
    id INT AUTO_INCREMENT PRIMARY KEY,

    offer_id INT,

    recipient VARCHAR(255),

    subject VARCHAR(255),

    body TEXT,

    status VARCHAR(20),

    created_at DATETIME,

    processed_at DATETIME
);

composer require phpoffice/phpspreadsheet

CREATE VIEW vw_offer_summary AS

SELECT

    o.id,
    o.numaroferta,
    o.firma,
    o.data,
    o.createdby,
    o.responsabil,
    o.departament,
    o.stareoferta,

    COUNT(e.id) item_count,

    COALESCE(
        SUM(
            CAST(
                NULLIF(e.total,'')
                AS DECIMAL(15,2)
            )
        ),
        0
    ) offer_total,

    o.email_sent,
    o.email_sent_at

FROM oferte o

LEFT JOIN elementeoferte e
    ON e.oferta=o.id

GROUP BY o.id;

ALTER TABLE users2
ADD COLUMN last_login DATETIME NULL;

ALTER TABLE users2
ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN failed_login_count INT DEFAULT 0,
ADD COLUMN locked_until DATETIME NULL;

CREATE TABLE app_settings
(
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT NULL,

    PRIMARY KEY (setting_key)
);


INSERT INTO app_settings
(setting_key, setting_value)
VALUES

('smtp_host',''),
('smtp_port','587'),
('smtp_user',''),
('smtp_password',''),

('smtp_from',''),
('smtp_from_name',''),

('default_email_recipient',''),

('pdf_company_name',''),

('pdf_footer','');


CREATE TABLE audit_logs
(
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NULL,

    user_name VARCHAR(128) NULL,

    action VARCHAR(100) NOT NULL,

    details TEXT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_created_at(created_at),
    INDEX idx_action(action),
    INDEX idx_user_name(user_name)
);

ALTER TABLE oferte ENGINE=InnoDB;
ALTER TABLE elementeoferte ENGINE=InnoDB;
ALTER TABLE users2 ENGINE=InnoDB;
ALTER TABLE audit_logs ENGINE=InnoDB;
ALTER TABLE email_history ENGINE=InnoDB;
ALTER TABLE app_settings ENGINE=InnoDB;

ALTER TABLE oferte
ADD COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE elementeoferte
ADD COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE users2
ADD COLUMN failed_login_count INT DEFAULT 0;

ALTER TABLE users2
ADD COLUMN locked_until DATETIME NULL;