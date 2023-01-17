CREATE TABLE IF NOT EXISTS `PREFIX_mt_sites` (
        `id_site` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11)  NOT NULL,
        `site_login` varchar(128)  NULL,
        `admin_email` varchar(128)  NULL,
        `db` varchar(64)  NULL,
        `prefix` varchar(64)  NULL,
        `md_pass` varchar(255)  NULL,
        `site_url` varchar(128)  NULL,
        `status` varchar(128)  NULL,
        `site_title` varchar(255)  NULL,
        `description` varchar(244)  NULL,
        `other_details` varchar(128)  NULL,
        `payload`  text  NULL,
        `date_add` datetime  NULL,
        `date_upd` timestamp NOT NULL DEFAULT NOW(),
         PRIMARY KEY (`id_site`) )
        ENGINE =InnoDB DEFAULT Charset =utf8 AUTO_INCREMENT=1 ;