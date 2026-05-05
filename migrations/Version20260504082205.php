<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504082205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certification (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INT NOT NULL, theme_id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, INDEX IDX_6C3C6D75A76ED395 (user_id), INDEX IDX_6C3C6D7559027487 (theme_id), INDEX IDX_6C3C6D75B03A8386 (created_by_id), INDEX IDX_6C3C6D75896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lesson_progress (id INT AUTO_INCREMENT NOT NULL, is_validated TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INT NOT NULL, lesson_id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, INDEX IDX_6A46B85FA76ED395 (user_id), INDEX IDX_6A46B85FCDF80196 (lesson_id), INDEX IDX_6A46B85FB03A8386 (created_by_id), INDEX IDX_6A46B85F896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7559027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE lesson_progress ADD CONSTRAINT FK_6A46B85FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE lesson_progress ADD CONSTRAINT FK_6A46B85FCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE lesson_progress ADD CONSTRAINT FK_6A46B85FB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE lesson_progress ADD CONSTRAINT FK_6A46B85F896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75A76ED395');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7559027487');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75B03A8386');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75896DBBDE');
        $this->addSql('ALTER TABLE lesson_progress DROP FOREIGN KEY FK_6A46B85FA76ED395');
        $this->addSql('ALTER TABLE lesson_progress DROP FOREIGN KEY FK_6A46B85FCDF80196');
        $this->addSql('ALTER TABLE lesson_progress DROP FOREIGN KEY FK_6A46B85FB03A8386');
        $this->addSql('ALTER TABLE lesson_progress DROP FOREIGN KEY FK_6A46B85F896DBBDE');
        $this->addSql('DROP TABLE certification');
        $this->addSql('DROP TABLE lesson_progress');
    }
}
