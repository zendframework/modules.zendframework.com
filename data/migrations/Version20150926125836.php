<?php

namespace ZfModulesMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150926125836 extends AbstractMigration
{
    public function getDescription()
    {
        return 'This migration sets up the initial database structure';
    }

    public function up(Schema $schema)
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS `module` (
                `module_id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text NOT NULL,
                `url` varchar(500) NOT NULL,
                `meta_data` blob,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                `photo_url` varchar(255) DEFAULT NULL,
                `owner` varchar(255) NOT NULL,
                PRIMARY KEY (`module_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');

        $this->addSql('
            CREATE TABLE IF NOT EXISTS `module_admin` (
                `user_id` int(11) NOT NULL,
                `module_id` int(11) NOT NULL,
                PRIMARY KEY (`user_id`,`module_id`),
                KEY `module_id` (`module_id`),
                CONSTRAINT `module_admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
                CONSTRAINT `module_admin_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');

        $this->addSql('
            CREATE TABLE IF NOT EXISTS `user` (
                `user_id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(255) DEFAULT NULL,
                `email` varchar(255) DEFAULT NULL,
                `display_name` varchar(50) DEFAULT NULL,
                `password` varchar(128) NOT NULL,
                `photo_url` varchar(255) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `state` int(2) DEFAULT NULL,
                PRIMARY KEY (`user_id`),
                UNIQUE KEY `username` (`username`),
                UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');

        $this->addSql('
            CREATE TABLE IF NOT EXISTS `user_provider` (
                `user_id` int(11) NOT NULL,
                `provider_id` varchar(50) NOT NULL,
                `provider` varchar(255) NOT NULL,
                PRIMARY KEY (`user_id`,`provider_id`),
                UNIQUE KEY `provider_id` (`provider_id`,`provider`),
                CONSTRAINT `user_provider_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    public function down(Schema $schema)
    {
        $tables = [
            'module',
            'module_admin',
            'user',
            'user_provider',
        ];

        foreach ($tables as $table) {
            $schema->dropTable($table);
        }
    }

    public function preUp(Schema $schema)
    {
        $this->pre();
    }

    public function postUp(Schema $schema)
    {
        $this->post();
    }

    public function preDown(Schema $schema)
    {
        $this->pre();
    }

    public function postDown(Schema $schema)
    {
        $this->post();
    }

    private function pre()
    {
        $this->addSql('SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT');
        $this->addSql('SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS');
        $this->addSql('SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION');
        $this->addSql('SET NAMES utf8');
        $this->addSql('SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0');
        $this->addSql("SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
        $this->addSql('SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0');
    }

    private function post()
    {
        $this->addSql('SET SQL_NOTES=@OLD_SQL_NOTES');
        $this->addSql('SET SQL_MODE=@OLD_SQL_MODE');
        $this->addSql('SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS');
        $this->addSql('SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT');
        $this->addSql('SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS');
        $this->addSql('SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION');
    }
}
