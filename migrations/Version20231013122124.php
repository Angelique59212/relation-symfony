<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231013122124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD editor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3316995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A3316995AC4C ON book (editor_id)');
        $this->addSql('ALTER TABLE editor DROP FOREIGN KEY FK_CCF1F1BA7DD8AC20');
        $this->addSql('DROP INDEX IDX_CCF1F1BA7DD8AC20 ON editor');
        $this->addSql('ALTER TABLE editor DROP books_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3316995AC4C');
        $this->addSql('DROP INDEX IDX_CBE5A3316995AC4C ON book');
        $this->addSql('ALTER TABLE book DROP editor_id');
        $this->addSql('ALTER TABLE editor ADD books_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE editor ADD CONSTRAINT FK_CCF1F1BA7DD8AC20 FOREIGN KEY (books_id) REFERENCES book (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CCF1F1BA7DD8AC20 ON editor (books_id)');
    }
}
