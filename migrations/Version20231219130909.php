<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219130909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_A9ED106219EB6921 ON portfolio');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A9ED106219EB6921 ON portfolio (client_id)');
        $this->addSql('DROP INDEX UNIQ_D4876C19EB6921 ON share_transaction');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4876C19EB6921 ON share_transaction (client_id)');
        $this->addSql('DROP INDEX UNIQ_723705D13E3A522E ON transaction');
        $this->addSql('DROP INDEX UNIQ_723705D1774744B0 ON transaction');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D13E3A522E ON transaction (emeteur_account_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D1774744B0 ON transaction (beneficiary_account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_723705D13E3A522E ON transaction');
        $this->addSql('DROP INDEX UNIQ_723705D1774744B0 ON transaction');
        $this->addSql('CREATE INDEX UNIQ_723705D13E3A522E ON transaction (emeteur_account_id)');
        $this->addSql('CREATE INDEX UNIQ_723705D1774744B0 ON transaction (beneficiary_account_id)');
        $this->addSql('DROP INDEX UNIQ_D4876C19EB6921 ON share_transaction');
        $this->addSql('CREATE INDEX UNIQ_D4876C19EB6921 ON share_transaction (client_id)');
        $this->addSql('DROP INDEX UNIQ_A9ED106219EB6921 ON portfolio');
        $this->addSql('CREATE INDEX UNIQ_A9ED106219EB6921 ON portfolio (client_id)');
    }
}
