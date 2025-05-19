-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema db_livroowl
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema db_livroowl
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `db_livroowl` DEFAULT CHARACTER SET utf8 ;
USE `db_livroowl` ;

-- -----------------------------------------------------
-- Table `db_livroowl`.`Livro`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_livroowl`.`Livro` (
  `idLivro` INT NOT NULL,
  `nome` VARCHAR(45) NOT NULL,
  `ISBN` VARCHAR(45) NOT NULL,
  `anoPub` YEAR(4) NOT NULL,
  `urlCover` TINYTEXT NOT NULL,
  PRIMARY KEY (`idLivro`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_livroowl`.`Genero`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_livroowl`.`Genero` (
  `idGenero` INT NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idGenero`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_livroowl`.`Autor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_livroowl`.`Autor` (
  `idAutor` VARCHAR(50) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idAutor`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_livroowl`.`Lista`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_livroowl`.`Lista` (
  `idLista` INT NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idLista`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_livroowl`.`Avaliacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_livroowl`.`Avaliacao` (
  `idAvaliacao` INT NOT NULL,
  `comentario` LONGTEXT NULL,
  `estrelas` DECIMAL(1,1) NOT NULL,
  `like` TINYINT NOT NULL,
  `dataAval` DATE NOT NULL,
  `idLivro` INT NOT NULL,
  PRIMARY KEY (`idAvaliacao`, `idLivro`),
  INDEX `fk_Avaliacao_Livro1_idx` (`idLivro` ASC) VISIBLE,
  CONSTRAINT `fk_Avaliacao_Livro1`
    FOREIGN KEY (`idLivro`)
    REFERENCES `db_livroowl`.`Livro` (`idLivro`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_livroowl`.`Autoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_livroowl`.`Autoria` (
  `Livro_idLivro` INT NOT NULL,
  `Autor_idAutor` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`Livro_idLivro`, `Autor_idAutor`),
  INDEX `fk_Livro_has_Autor_Autor1_idx` (`Autor_idAutor` ASC) VISIBLE,
  INDEX `fk_Livro_has_Autor_Livro_idx` (`Livro_idLivro` ASC) VISIBLE,
  CONSTRAINT `fk_Livro_has_Autor_Livro`
    FOREIGN KEY (`Livro_idLivro`)
    REFERENCES `db_livroowl`.`Livro` (`idLivro`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Livro_has_Autor_Autor1`
    FOREIGN KEY (`Autor_idAutor`)
    REFERENCES `db_livroowl`.`Autor` (`idAutor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_livroowl`.`LivroGenero`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_livroowl`.`LivroGenero` (
  `Livro_idLivro` INT NOT NULL,
  `Genero_idGenero` INT NOT NULL,
  PRIMARY KEY (`Livro_idLivro`, `Genero_idGenero`),
  INDEX `fk_Livro_has_Genero_Genero1_idx` (`Genero_idGenero` ASC) VISIBLE,
  INDEX `fk_Livro_has_Genero_Livro1_idx` (`Livro_idLivro` ASC) VISIBLE,
  CONSTRAINT `fk_Livro_has_Genero_Livro1`
    FOREIGN KEY (`Livro_idLivro`)
    REFERENCES `db_livroowl`.`Livro` (`idLivro`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Livro_has_Genero_Genero1`
    FOREIGN KEY (`Genero_idGenero`)
    REFERENCES `db_livroowl`.`Genero` (`idGenero`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_livroowl`.`LivroLista`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_livroowl`.`LivroLista` (
  `Livro_idLivro` INT NOT NULL,
  `Lista_idLista` INT NOT NULL,
  PRIMARY KEY (`Livro_idLivro`, `Lista_idLista`),
  INDEX `fk_Livro_has_Lista_Lista1_idx` (`Lista_idLista` ASC) VISIBLE,
  INDEX `fk_Livro_has_Lista_Livro1_idx` (`Livro_idLivro` ASC) VISIBLE,
  CONSTRAINT `fk_Livro_has_Lista_Livro1`
    FOREIGN KEY (`Livro_idLivro`)
    REFERENCES `db_livroowl`.`Livro` (`idLivro`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Livro_has_Lista_Lista1`
    FOREIGN KEY (`Lista_idLista`)
    REFERENCES `db_livroowl`.`Lista` (`idLista`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
