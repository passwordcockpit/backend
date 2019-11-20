<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120124547 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE token_user (user_id INT NOT NULL, token VARCHAR(500) DEFAULT NULL, last_login DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_EF97E32B5F37A13B (token), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE login_request (request_id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(100) DEFAULT NULL, attempt_date DATETIME DEFAULT NULL, username VARCHAR(100) DEFAULT NULL, PRIMARY KEY(request_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (user_id INT AUTO_INCREMENT NOT NULL, username VARCHAR(45) NOT NULL, password VARCHAR(200) DEFAULT NULL, name VARCHAR(45) DEFAULT NULL, surname VARCHAR(45) DEFAULT NULL, language VARCHAR(2) NOT NULL, phone VARCHAR(45) DEFAULT NULL, email VARCHAR(45) DEFAULT NULL, enabled TINYINT(1) NOT NULL, change_password TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX username_UNIQUE (username), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (user_id INT NOT NULL, manage_users TINYINT(1) DEFAULT NULL, create_folders TINYINT(1) DEFAULT NULL, access_all_folders TINYINT(1) DEFAULT NULL, view_logs TINYINT(1) DEFAULT NULL, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE folder_user (folder_user_id INT AUTO_INCREMENT NOT NULL, folder_id INT DEFAULT NULL, user_id INT DEFAULT NULL, access SMALLINT DEFAULT NULL, INDEX IDX_940CF05C162CB942 (folder_id), INDEX IDX_940CF05CA76ED395 (user_id), PRIMARY KEY(folder_user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE folder (folder_id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, parent_id INT DEFAULT NULL, PRIMARY KEY(folder_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password (password_id INT AUTO_INCREMENT NOT NULL, folder_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, icon VARCHAR(45) DEFAULT NULL, description VARCHAR(4000) DEFAULT NULL, username VARCHAR(100) DEFAULT NULL, password VARCHAR(1000) DEFAULT NULL, url VARCHAR(100) DEFAULT NULL, tags VARCHAR(400) DEFAULT NULL, last_modification_date DATETIME DEFAULT NULL, frontend_crypted TINYINT(1) DEFAULT NULL, INDEX IDX_35C246D5162CB942 (folder_id), PRIMARY KEY(password_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (log_id INT AUTO_INCREMENT NOT NULL, password_id INT DEFAULT NULL, user_id INT DEFAULT NULL, action_date DATETIME DEFAULT NULL, action VARCHAR(4000) DEFAULT NULL, INDEX IDX_8F3F68C53E4A79C1 (password_id), INDEX IDX_8F3F68C5A76ED395 (user_id), PRIMARY KEY(log_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (file_id INT AUTO_INCREMENT NOT NULL, password_id INT DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, filename VARCHAR(1000) DEFAULT NULL, extension VARCHAR(200) DEFAULT NULL, creation_date DATETIME DEFAULT NULL, INDEX IDX_8C9F36103E4A79C1 (password_id), PRIMARY KEY(file_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE token_user ADD CONSTRAINT FK_EF97E32BA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE folder_user ADD CONSTRAINT FK_940CF05C162CB942 FOREIGN KEY (folder_id) REFERENCES folder (folder_id)');
        $this->addSql('ALTER TABLE folder_user ADD CONSTRAINT FK_940CF05CA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE password ADD CONSTRAINT FK_35C246D5162CB942 FOREIGN KEY (folder_id) REFERENCES folder (folder_id)');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C53E4A79C1 FOREIGN KEY (password_id) REFERENCES password (password_id)');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36103E4A79C1 FOREIGN KEY (password_id) REFERENCES password (password_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE token_user DROP FOREIGN KEY FK_EF97E32BA76ED395');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAA76ED395');
        $this->addSql('ALTER TABLE folder_user DROP FOREIGN KEY FK_940CF05CA76ED395');
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5A76ED395');
        $this->addSql('ALTER TABLE folder_user DROP FOREIGN KEY FK_940CF05C162CB942');
        $this->addSql('ALTER TABLE password DROP FOREIGN KEY FK_35C246D5162CB942');
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C53E4A79C1');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36103E4A79C1');
        $this->addSql('DROP TABLE token_user');
        $this->addSql('DROP TABLE login_request');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE folder_user');
        $this->addSql('DROP TABLE folder');
        $this->addSql('DROP TABLE password');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE file');
    }
}
