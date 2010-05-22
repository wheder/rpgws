SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `wheder` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
SHOW WARNINGS;
USE `wheder`;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_users` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_users` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `nick` VARCHAR(255) NOT NULL ,
  `pass` CHAR(40) NOT NULL ,
  `mail` VARCHAR(255) NOT NULL ,
  `deleted` BIT NOT NULL ,
  `confirmed` BIT NOT NULL ,
  `last_action` DATETIME NULL ,
  `last_ip` INT UNSIGNED NOT NULL ,
  `born` DATE NOT NULL ,
  `unsuccessful_login_attempts` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `idx_users_nick` ON `wheder`.`RPGWS_users` (`nick` ASC) ;

SHOW WARNINGS;
CREATE INDEX `idx_users_deleted` ON `wheder`.`RPGWS_users` (`deleted` ASC) ;

SHOW WARNINGS;
CREATE INDEX `idx_users_confirmed` ON `wheder`.`RPGWS_users` (`confirmed` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_modules`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_modules` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_modules` (
  `module_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`module_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_groups` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_groups` (
  `group_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `module_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`group_id`) ,
  CONSTRAINT `fk_groups_modules1`
    FOREIGN KEY (`module_id` )
    REFERENCES `wheder`.`RPGWS_modules` (`module_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE INDEX `fk_groups_modules1` ON `wheder`.`RPGWS_groups` (`module_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_user_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_user_group` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_user_group` (
  `user_id` INT UNSIGNED NOT NULL ,
  `group_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`user_id`, `group_id`) ,
  CONSTRAINT `fk_usergroup_users_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `wheder`.`RPGWS_users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usergroup_groups_id`
    FOREIGN KEY (`group_id` )
    REFERENCES `wheder`.`RPGWS_groups` (`group_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE INDEX `fk_usergroup_users_id` ON `wheder`.`RPGWS_user_group` (`user_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_usergroup_groups_id` ON `wheder`.`RPGWS_user_group` (`group_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_modules_rights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_modules_rights` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_modules_rights` (
  `modules_right_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `module_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`modules_right_id`) ,
  CONSTRAINT `fk_modules_rights_parent`
    FOREIGN KEY (`module_id` )
    REFERENCES `wheder`.`RPGWS_modules` (`module_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE INDEX `idx_module_parent` ON `wheder`.`RPGWS_modules_rights` (`module_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_modules_rights_parent` ON `wheder`.`RPGWS_modules_rights` (`module_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_rights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_rights` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_rights` (
  `module_right` INT UNSIGNED NOT NULL ,
  `value` BIT NOT NULL ,
  `group_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`module_right`, `group_id`) ,
  CONSTRAINT `fk_rights_modules_right`
    FOREIGN KEY (`module_right` )
    REFERENCES `wheder`.`RPGWS_modules_rights` (`modules_right_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rights_group`
    FOREIGN KEY (`group_id` )
    REFERENCES `wheder`.`RPGWS_groups` (`group_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE INDEX `idx_right_value` ON `wheder`.`RPGWS_rights` (`value` ASC) ;

SHOW WARNINGS;
CREATE INDEX `idx_modules_right` ON `wheder`.`RPGWS_rights` (`module_right` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_rights_modules_right` ON `wheder`.`RPGWS_rights` (`module_right` ASC) ;

SHOW WARNINGS;
CREATE INDEX `idx_rights_group` ON `wheder`.`RPGWS_rights` (`group_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_rights_group` ON `wheder`.`RPGWS_rights` (`group_id` ASC) ;

SHOW WARNINGS;
CREATE UNIQUE INDEX `uq_rights` ON `wheder`.`RPGWS_rights` (`module_right` ASC, `group_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_login_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_login_log` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_login_log` (
  `log_id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT UNSIGNED NOT NULL ,
  `ip` INT UNSIGNED NOT NULL ,
  `time` DATETIME NOT NULL ,
  `success` BIT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`log_id`) ,
  CONSTRAINT `fk_login_log_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `wheder`.`RPGWS_users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_login_log_users1` ON `wheder`.`RPGWS_login_log` (`user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_user_detail_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_user_detail_types` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_user_detail_types` (
  `user_detail_type_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`user_detail_type_id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_user_detail`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_user_detail` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_user_detail` (
  `user_id` INT UNSIGNED NOT NULL ,
  `user_detail_type_id` INT UNSIGNED NOT NULL ,
  `value` VARCHAR(255) NOT NULL ,
  `public` BIT NOT NULL ,
  PRIMARY KEY (`user_id`, `user_detail_type_id`) ,
  CONSTRAINT `fk_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `wheder`.`RPGWS_users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_detail_type`
    FOREIGN KEY (`user_detail_type_id` )
    REFERENCES `wheder`.`RPGWS_user_detail_types` (`user_detail_type_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_user_id` ON `wheder`.`RPGWS_user_detail` (`user_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_user_detail_type` ON `wheder`.`RPGWS_user_detail` (`user_detail_type_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_drd_classes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_drd_classes` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_drd_classes` (
  `drd_classes_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`drd_classes_id`) ,
  CONSTRAINT `fk_parent_id`
    FOREIGN KEY (`parent_id` )
    REFERENCES `wheder`.`RPGWS_drd_classes` (`drd_classes_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `idx_parent_id` ON `wheder`.`RPGWS_drd_classes` (`parent_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_parent_id` ON `wheder`.`RPGWS_drd_classes` (`parent_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_drd_races`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_drd_races` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_drd_races` (
  `drd_races_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`drd_races_id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_drd_characters`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_drd_characters` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_drd_characters` (
  `drd_character_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `owner_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NULL ,
  `race_id` INT UNSIGNED NOT NULL ,
  `class_id` INT UNSIGNED NULL ,
  `hit_points` INT NOT NULL ,
  `mana` INT NULL ,
  `items` TEXT NULL COMMENT 'mozna by se mohlo prehodit na reference... v budoucnu' ,
  PRIMARY KEY (`drd_character_id`) ,
  CONSTRAINT `fk_owner_id`
    FOREIGN KEY (`owner_id` )
    REFERENCES `wheder`.`RPGWS_users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_class_id`
    FOREIGN KEY (`class_id` )
    REFERENCES `wheder`.`RPGWS_drd_classes` (`drd_classes_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_race_id`
    FOREIGN KEY (`race_id` )
    REFERENCES `wheder`.`RPGWS_drd_races` (`drd_races_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `idx_owner_id` ON `wheder`.`RPGWS_drd_characters` (`owner_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_owner_id` ON `wheder`.`RPGWS_drd_characters` (`owner_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `idx_race_id` ON `wheder`.`RPGWS_drd_characters` (`race_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `idx_class_id` ON `wheder`.`RPGWS_drd_characters` (`class_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_class_id` ON `wheder`.`RPGWS_drd_characters` (`class_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_race_id` ON `wheder`.`RPGWS_drd_characters` (`race_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_drd_quests`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_drd_quests` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_drd_quests` (
  `drd_quest_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `game_master_id` INT UNSIGNED NULL ,
  `description` TEXT NULL ,
  `active` BIT NOT NULL ,
  PRIMARY KEY (`drd_quest_id`) ,
  CONSTRAINT `fk_game_master_id`
    FOREIGN KEY (`game_master_id` )
    REFERENCES `wheder`.`RPGWS_users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `idx_game_master_id` ON `wheder`.`RPGWS_drd_quests` (`game_master_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_game_master_id` ON `wheder`.`RPGWS_drd_quests` (`game_master_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_drd_quest_members`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_drd_quest_members` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_drd_quest_members` (
  `drd_quest_id` INT UNSIGNED NOT NULL ,
  `drd_character_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`drd_quest_id`, `drd_character_id`) ,
  CONSTRAINT `fk_quest_id`
    FOREIGN KEY (`drd_quest_id` )
    REFERENCES `wheder`.`RPGWS_drd_quests` (`drd_quest_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_character_id`
    FOREIGN KEY (`drd_character_id` )
    REFERENCES `wheder`.`RPGWS_drd_characters` (`drd_character_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_quest_id` ON `wheder`.`RPGWS_drd_quest_members` (`drd_quest_id` ASC) ;

SHOW WARNINGS;
CREATE UNIQUE INDEX `uq_character_id` ON `wheder`.`RPGWS_drd_quest_members` (`drd_character_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_character_id` ON `wheder`.`RPGWS_drd_quest_members` (`drd_character_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_drd_quest_posts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_drd_quest_posts` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_drd_quest_posts` (
  `drd_quest_post_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `belongs_to_quest_id` INT UNSIGNED NOT NULL ,
  `author_character_id` INT UNSIGNED NULL ,
  `origin_time` DATETIME NOT NULL ,
  `content` TEXT NOT NULL ,
  `is_whisper` BIT NOT NULL ,
  `author_user_id` INT UNSIGNED NULL COMMENT 'aby bylo poznat kdo to napsal . . . jaky pj\n\njeste nevim jestli bude nebo nebude nullable' ,
  PRIMARY KEY (`drd_quest_post_id`) ,
  CONSTRAINT `fk_posts_quest_id`
    FOREIGN KEY (`belongs_to_quest_id` )
    REFERENCES `wheder`.`RPGWS_drd_quests` (`drd_quest_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_author_character_id`
    FOREIGN KEY (`author_character_id` )
    REFERENCES `wheder`.`RPGWS_drd_characters` (`drd_character_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_author_user_id`
    FOREIGN KEY (`author_user_id` )
    REFERENCES `wheder`.`RPGWS_users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `idx_belongs_to_quest_id` ON `wheder`.`RPGWS_drd_quest_posts` (`belongs_to_quest_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `idx_author_character_id` ON `wheder`.`RPGWS_drd_quest_posts` (`author_character_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_posts_quest_id` ON `wheder`.`RPGWS_drd_quest_posts` (`belongs_to_quest_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_author_character_id` ON `wheder`.`RPGWS_drd_quest_posts` (`author_character_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `idx_author_user_id` ON `wheder`.`RPGWS_drd_quest_posts` (`author_user_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_author_user_id` ON `wheder`.`RPGWS_drd_quest_posts` (`author_user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `wheder`.`RPGWS_drd_quest_whisper`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wheder`.`RPGWS_drd_quest_whisper` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `wheder`.`RPGWS_drd_quest_whisper` (
  `drd_quest_post_id` BIGINT UNSIGNED NOT NULL ,
  `written_for_drd_character_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`drd_quest_post_id`, `written_for_drd_character_id`) ,
  CONSTRAINT `fk_post_id`
    FOREIGN KEY (`drd_quest_post_id` )
    REFERENCES `wheder`.`RPGWS_drd_quest_posts` (`drd_quest_post_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_whispered_to`
    FOREIGN KEY (`written_for_drd_character_id` )
    REFERENCES `wheder`.`RPGWS_drd_characters` (`drd_character_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_post_id` ON `wheder`.`RPGWS_drd_quest_whisper` (`drd_quest_post_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_whispered_to` ON `wheder`.`RPGWS_drd_quest_whisper` (`written_for_drd_character_id` ASC) ;

SHOW WARNINGS;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
