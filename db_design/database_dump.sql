-- MySQL Script generated by MySQL Workbench
-- 03/10/19 16:25:58
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema coinless
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema coinless
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `coinless` DEFAULT CHARACTER SET utf8 ;
USE `coinless` ;

-- -----------------------------------------------------
-- Table `coinless`.`cor_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `coinless`.`cor_users` (
  `userid` VARCHAR(45) NOT NULL COMMENT 'md5(replace(UUID(), \'-\', \'\'))',
  `username` VARCHAR(16) NULL,
  `email` VARCHAR(150) NULL,
  `phone_number` VARCHAR(45) NULL,
  `password` VARCHAR(32) NOT NULL,
  `urgency_code` VARCHAR(8) NULL,
  `qrcode` VARCHAR(45) NOT NULL,
  `firstname` VARCHAR(45) NOT NULL,
  `lastname` VARCHAR(45) NOT NULL,
  `birth_date` DATE NULL,
  `address` VARCHAR(150) NULL,
  `picture` VARCHAR(255) NULL COMMENT 'Image de profil',
  `register_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Update at any login or transaction',
  `email_token` VARCHAR(45) NULL,
  `phone_code` CHAR(7) NULL,
  PRIMARY KEY (`userid`),
  UNIQUE INDEX `userid_UNIQUE` (`userid` ASC),
  UNIQUE INDEX `phone_number_UNIQUE` (`phone_number` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  UNIQUE INDEX `qrcode_UNIQUE` (`qrcode` ASC),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `coinless`.`cor_subscriptions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `coinless`.`cor_subscriptions` (
  `reg_id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'It will be used on detailed transactions.',
  `userid` VARCHAR(50) NOT NULL,
  `date_reg` TIMESTAMP NOT NULL,
  `cost_value` INT(10) NOT NULL COMMENT 'Montant',
  `number_days` INT(2) NOT NULL COMMENT 'Nombre de jour de l\'abonnement.',
  `reg_status` TINYINT(1) NOT NULL COMMENT 'Statut actif/non-actif de l\'abonnement',
  `user_deleted` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'L\'utilisateur peut choisir de supprimer l\'historique de ses abonnements, mais nous gardons la trace.',
  PRIMARY KEY (`reg_id`))
ENGINE = InnoDB
COMMENT = 'Abonnements des utilisateurs. une routine-trigger sera mise en place pour le calcul entre la date courante et celle de date_reg, faire une comparaison avec number_days et changer le status de reg_status a true ou false.';


-- -----------------------------------------------------
-- Table `coinless`.`cor_accounts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `coinless`.`cor_accounts` (
  `id_account` BIGINT NOT NULL,
  `id_transact` BIGINT NOT NULL,
  `userid` VARCHAR(45) NOT NULL,
  `opposal` VARCHAR(45) NOT NULL COMMENT 'Correspondant de la transaction(le Systeme ie coinless ou un autre utilisateur',
  `amount` INT(6) NOT NULL,
  `type` ENUM('credit', 'debit') NOT NULL,
  `date_insert` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_account`))
ENGINE = InnoDB
COMMENT = 'Table du compte utilisateur dans laquelle il credite son compte. Il peut le durant la periode de son abonnement .initialisee au montant null a la creation de l\'utilisateur. Ses depots seront pris en compte ici.';


-- -----------------------------------------------------
-- Table `coinless`.`cor_transactions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `coinless`.`cor_transactions` (
  `id_transact` BIGINT NOT NULL AUTO_INCREMENT,
  `user_from` VARCHAR(45) NOT NULL COMMENT 'Id de celui qui envoi. compte debitaire',
  `user_to` VARCHAR(45) NOT NULL COMMENT 'Id de celui qui recoit',
  `amount` INT(6) NOT NULL COMMENT 'Montant de la transaction',
  `description` VARCHAR(255) NULL,
  `date_transact` TIMESTAMP NOT NULL,
  `code_transact` VARCHAR(45) NOT NULL COMMENT 'Code de la transaction',
  `status` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_transact`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
