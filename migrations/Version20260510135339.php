<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260510135339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY `FK_6BAF787089312FE9`');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF787089312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recette DROP FOREIGN KEY `FK_49BB639060BB6FE6`');
        $this->addSql('ALTER TABLE recette ADD CONSTRAINT FK_49BB639060BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recette_tag_recette DROP FOREIGN KEY `FK_84F94B732C2AADB8`');
        $this->addSql('ALTER TABLE recette_tag_recette ADD CONSTRAINT FK_84F94B732C2AADB8 FOREIGN KEY (tag_recette_id) REFERENCES tag_recette (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF787089312FE9');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT `FK_6BAF787089312FE9` FOREIGN KEY (recette_id) REFERENCES recette (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE recette DROP FOREIGN KEY FK_49BB639060BB6FE6');
        $this->addSql('ALTER TABLE recette ADD CONSTRAINT `FK_49BB639060BB6FE6` FOREIGN KEY (auteur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE recette_tag_recette DROP FOREIGN KEY FK_84F94B732C2AADB8');
        $this->addSql('ALTER TABLE recette_tag_recette ADD CONSTRAINT `FK_84F94B732C2AADB8` FOREIGN KEY (tag_recette_id) REFERENCES tag_recette (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
