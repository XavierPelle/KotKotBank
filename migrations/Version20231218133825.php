<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231218133825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, balance DOUBLE PRECISION DEFAULT NULL, overdraft INT DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, date DATETIME DEFAULT NULL, INDEX IDX_7D3656A419EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, lastname VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, investor_profil VARCHAR(50) NOT NULL, registration_date DATETIME NOT NULL, last_connexion DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) DEFAULT NULL, domain VARCHAR(150) DEFAULT NULL, share_price DOUBLE PRECISION DEFAULT NULL, share_quantity INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) DEFAULT NULL, domain VARCHAR(150) DEFAULT NULL, probability DOUBLE PRECISION DEFAULT NULL, impact DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portefolio (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, share_name VARCHAR(150) DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_65BAA47C19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share_transaction (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, share_name VARCHAR(150) DEFAULT NULL, share_price DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_D4876C19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, emeteur_account_id INT DEFAULT NULL, beneficiary_account_id INT DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, date DATETIME DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_723705D13E3A522E (emeteur_account_id), UNIQUE INDEX UNIQ_723705D1774744B0 (beneficiary_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE portefolio ADD CONSTRAINT FK_65BAA47C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE share_transaction ADD CONSTRAINT FK_D4876C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D13E3A522E FOREIGN KEY (emeteur_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1774744B0 FOREIGN KEY (beneficiary_account_id) REFERENCES account (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A419EB6921');
        $this->addSql('ALTER TABLE portefolio DROP FOREIGN KEY FK_65BAA47C19EB6921');
        $this->addSql('ALTER TABLE share_transaction DROP FOREIGN KEY FK_D4876C19EB6921');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D13E3A522E');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1774744B0');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE portefolio');
        $this->addSql('DROP TABLE share_transaction');
        $this->addSql('DROP TABLE transaction');
    }
}
