<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210108175534 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auteur (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, livre_id INT NOT NULL, commandeur_id INT NOT NULL, envoyeur_id INT NOT NULL, envoyer TINYINT(1) NOT NULL, date VARCHAR(20) NOT NULL, valider TINYINT(1) NOT NULL, refuser TINYINT(1) NOT NULL, INDEX IDX_6EEAA67D37D925CB (livre_id), INDEX IDX_6EEAA67D996F9D6F (commandeur_id), INDEX IDX_6EEAA67D4795A786 (envoyeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commune (id INT AUTO_INCREMENT NOT NULL, nom_commune VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE departement (id INT AUTO_INCREMENT NOT NULL, nom_departement VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livre (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, auteur_id INT NOT NULL, titre VARCHAR(100) NOT NULL, date_de_publication VARCHAR(50) NOT NULL, langue VARCHAR(90) NOT NULL, nombre_pages INT NOT NULL, image VARCHAR(255) NOT NULL, nombre_points INT NOT NULL, disponible TINYINT(1) NOT NULL, saisie TINYINT(1) NOT NULL, isbn VARCHAR(30) NOT NULL, INDEX IDX_AC634F99BCF5E72D (categorie_id), INDEX IDX_AC634F9960BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pays (id INT AUTO_INCREMENT NOT NULL, nom_pays VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, nom_region VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, commune_id INT NOT NULL, region_id INT NOT NULL, departement_id INT NOT NULL, pays_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, pseudo VARCHAR(100) NOT NULL, civilite VARCHAR(50) NOT NULL, code_postal VARCHAR(60) NOT NULL, points INT NOT NULL, vacance TINYINT(1) NOT NULL, is_verified TINYINT(1) NOT NULL, telephone VARCHAR(50) NOT NULL, new_demande INT NOT NULL, new_envoie INT NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649131A4F72 (commune_id), INDEX IDX_8D93D64998260155 (region_id), INDEX IDX_8D93D649CCF9E01E (departement_id), INDEX IDX_8D93D649A6E44244 (pays_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_livre (user_id INT NOT NULL, livre_id INT NOT NULL, INDEX IDX_4EA1B4C1A76ED395 (user_id), INDEX IDX_4EA1B4C137D925CB (livre_id), PRIMARY KEY(user_id, livre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D37D925CB FOREIGN KEY (livre_id) REFERENCES livre (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D996F9D6F FOREIGN KEY (commandeur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D4795A786 FOREIGN KEY (envoyeur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE livre ADD CONSTRAINT FK_AC634F99BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE livre ADD CONSTRAINT FK_AC634F9960BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649131A4F72 FOREIGN KEY (commune_id) REFERENCES commune (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64998260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE user_livre ADD CONSTRAINT FK_4EA1B4C1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_livre ADD CONSTRAINT FK_4EA1B4C137D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livre DROP FOREIGN KEY FK_AC634F9960BB6FE6');
        $this->addSql('ALTER TABLE livre DROP FOREIGN KEY FK_AC634F99BCF5E72D');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649131A4F72');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649CCF9E01E');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D37D925CB');
        $this->addSql('ALTER TABLE user_livre DROP FOREIGN KEY FK_4EA1B4C137D925CB');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649A6E44244');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64998260155');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D996F9D6F');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D4795A786');
        $this->addSql('ALTER TABLE user_livre DROP FOREIGN KEY FK_4EA1B4C1A76ED395');
        $this->addSql('DROP TABLE auteur');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commune');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE livre');
        $this->addSql('DROP TABLE pays');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_livre');
    }
}
